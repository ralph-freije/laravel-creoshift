<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $flights = QueryBuilder::for(Flight::class)
            ->allowedFilters([
                'number',
                'departure_city',
                'arrival_city',
                'departure_time',
                'arrival_time',
            ])
            ->allowedSorts([
                'id',
                'number',
                'departure_city',
                'arrival_city',
                'departure_time',
                'arrival_time',
                'created_at',
            ])
            ->defaultSort('id')
            ->paginate($request->get('per_page', 15))
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $flights,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:255|unique:flights,number',
            'departure_city' => 'required|string|max:255',
            'arrival_city' => 'required|string|max:255',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
        ]);

        $flight = Flight::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Flight created successfully',
            'data' => $flight,
        ], 201);
    }

    public function show(Flight $flight)
    {
        return response()->json([
            'success' => true,
            'data' => $flight,
        ]);
    }

    public function update(Request $request, Flight $flight)
    {
        $validated = $request->validate([
            'number' => 'sometimes|required|string|max:255|unique:flights,number,' . $flight->id,
            'departure_city' => 'sometimes|required|string|max:255',
            'arrival_city' => 'sometimes|required|string|max:255',
            'departure_time' => 'sometimes|required|date',
            'arrival_time' => 'sometimes|required|date|after:departure_time',
        ]);

        $flight->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Flight updated successfully',
            'data' => $flight,
        ]);
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();

        return response()->json([
            'success' => true,
            'message' => 'Flight deleted successfully',
        ]);
    }
}