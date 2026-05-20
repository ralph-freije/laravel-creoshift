<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $query = Flight::query();

        if ($request->has('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%')
                    ->orWhere('departure_city', 'like', '%' . $search . '%')
                    ->orWhere('arrival_city', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('number')) {
            $query->where('number', 'like', '%' . $request->number . '%');
        }

        if ($request->has('departure_city')) {
            $query->where('departure_city', 'like', '%' . $request->departure_city . '%');
        }

        if ($request->has('arrival_city')) {
            $query->where('arrival_city', 'like', '%' . $request->arrival_city . '%');
        }

        $allowedSortFields = [
            'id',
            'number',
            'departure_city',
            'arrival_city',
            'departure_time',
            'arrival_time',
            'created_at',
        ];

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'asc');

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'id';
        }

        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 15);

        return response()->json([
            'success' => true,
            'data' => $query->paginate($perPage),
        ]);
    }

    public function passengers(Request $request, Flight $flight)
    {
        $query = $flight->passengers();

        if ($request->has('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $allowedSortFields = [
            'passengers.id',
            'first_name',
            'last_name',
            'email',
            'dob',
            'passport_expiry_date',
        ];

        $sortBy = $request->get('sort_by', 'passengers.id');
        $sortDir = $request->get('sort_dir', 'asc');

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'passengers.id';
        }

        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 15);

        return response()->json([
            'success' => true,
            'flight' => $flight,
            'data' => $query->paginate($perPage),
        ]);
    }
}