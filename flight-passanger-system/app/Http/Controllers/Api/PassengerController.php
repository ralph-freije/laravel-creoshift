<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PassengerController extends Controller
{
    private function rememberPassengerCacheKey(string $cacheKey): void
    {
        $keys = Cache::get('passenger_cache_keys', []);

        if (!in_array($cacheKey, $keys)) {
            $keys[] = $cacheKey;
            Cache::put('passenger_cache_keys', $keys, now()->addHours(1));
        }
    }

    private function clearPassengerCache(): void
    {
        $keys = Cache::get('passenger_cache_keys', []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget('passenger_cache_keys');
    }

    public function index(Request $request)
    {
        $cacheKey = 'passengers_' . md5(json_encode($request->query()));

        $this->rememberPassengerCacheKey($cacheKey);

        $passengers = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            return QueryBuilder::for(Passenger::class)
                ->allowedFilters([
                    'first_name',
                    'last_name',
                    'email',
                    'dob',
                    'passport_expiry_date',

                    AllowedFilter::callback('flight_id', function ($query, $value) {
                        $query->whereHas('flights', function ($q) use ($value) {
                            $q->where('flights.id', $value);
                        });
                    }),
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
        });

        return response()->json([
            'success' => true,
            'cached' => true,
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
            'email' => [
                'required',
                'email',
                Rule::unique('passengers', 'email')->whereNull('deleted_at'),
            ],
            'password' => 'required|string|min:6',
            'dob' => 'required|date|before:today',
            'passport_expiry_date' => 'required|date|after:today',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $passenger = Passenger::create($validated);

        $this->clearPassengerCache();

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
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('passengers', 'email')
                    ->whereNull('deleted_at')
                    ->ignore($passenger->id),
            ],
            'password' => 'sometimes|required|string|min:6',
            'dob' => 'sometimes|required|date|before:today',
            'passport_expiry_date' => 'sometimes|required|date|after:today',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $passenger->update($validated);

        $this->clearPassengerCache();

        return response()->json([
            'success' => true,
            'message' => 'Passenger updated successfully',
            'data' => $passenger,
        ]);
    }

    public function destroy(Passenger $passenger)
    {
        $passenger->delete();

        $this->clearPassengerCache();

        return response()->json([
            'success' => true,
            'message' => 'Passenger deleted successfully',
        ]);
    }
}