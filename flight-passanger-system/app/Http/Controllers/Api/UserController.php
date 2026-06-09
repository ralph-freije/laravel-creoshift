<?php

namespace App\Http\Controllers\Api;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                'name',
                'email',
            ])
            ->allowedSorts([
                'id',
                'name',
                'email',
                'created_at',
            ])
            ->defaultSort('id')
            ->paginate($request->get('per_page', 15))
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => 'required|in:admin,user',
        ]);

        $role = $validated['role'];
        unset($validated['role']);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->assignRole($role);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => [
                'user' => $user,
                'roles' => $user->getRoleNames(),
            ],
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->ignore($user->id),
            ],
            'password' => [
                'sometimes',
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => 'sometimes|required|in:admin,user',
        ]);

        $role = null;

        if (isset($validated['role'])) {
            $role = $validated['role'];
            unset($validated['role']);
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if ($role) {
            $user->syncRoles([$role]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => [
                'user' => $user,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}