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

    public function passengers(Request $request, Flight $flight)
    {
        $passengers = QueryBuilder::for($flight->passengers())
            ->allowedFilters([
                'first_name',
                'last_name',
                'email',
                'dob',
                'passport_expiry_date',
            ])
            ->allowedSorts([
                'passengers.id',
                'first_name',
                'last_name',
                'email',
                'dob',
                'passport_expiry_date',
            ])
            ->defaultSort('passengers.id')
            ->paginate($request->get('per_page', 15))
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'flight' => $flight,
            'data' => $passengers,
        ]);
    }
}