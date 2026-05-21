<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;

class PassengerController extends Controller
{
    public function index(Request $request)
    {
        $passengers = QueryBuilder::for(Passenger::class)
            ->allowedFilters([
                'first_name',
                'last_name',
                'email',
                'dob',
                'passport_expiry_date',
            ])
            ->allowedSorts([
                'id',
                'first_name',
                'last_name',
                'email',
                'dob',
                'passport_expiry_date',
                'created_at',
            ])
            ->defaultSort('id')
            ->paginate($request->get('per_page', 15))
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $passengers,
        ]);
    }

    public function show(Passenger $passenger)
    {
        return response()->json([
            'success' => true,
            'data' => $passenger,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:passengers,email',
            'password' => 'required|string|min:6',
            'dob' => 'required|date',
            'passport_expiry_date' => 'required|date|after:today',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $passenger = Passenger::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Passenger created successfully',
            'data' => $passenger,
        ], 201);
    }

    public function update(Request $request, Passenger $passenger)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:passengers,email,' . $passenger->id,
            'password' => 'sometimes|required|string|min:6',
            'dob' => 'sometimes|required|date',
            'passport_expiry_date' => 'sometimes|required|date|after:today',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $passenger->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Passenger updated successfully',
            'data' => $passenger,
        ]);
    }

    public function destroy(Passenger $passenger)
    {
        $passenger->delete();

        return response()->json([
            'success' => true,
            'message' => 'Passenger deleted successfully',
        ]);
    }
}