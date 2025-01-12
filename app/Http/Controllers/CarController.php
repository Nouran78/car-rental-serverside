<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function addCar(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'price_per_day' => 'required|numeric|min:0',
            'availability_status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $car = new Car();
        $car->name = $validatedData['name'];
        $car->type = $validatedData['type'];
        $car->price_per_day = $validatedData['price_per_day'];
        $car->availability_status = $validatedData['availability_status'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
            $car->image = $path;
        }

        $car->save();

        return response()->json([
            'message' => 'Car added successfully!',
            'car' => $car,
        ], 201);
    }

    public function listAllCars()
    {
        $cars = Car::all();

        return response()->json(['cars' => $cars], 200);
    }

    public function showCar($id)
    {
        $car = Car::find($id);


        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        return response()->json(['car' => $car], 200);
    }

    public function searchAndFilterCars(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'availability_status' => 'nullable|boolean',
        ]);

        $query = Car::query();

        if ($request->filled('name')) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->name) . '%']);
            // $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('price_min')) {
            $query->where('price_per_day', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price_per_day', '<=', $request->price_max);
        }

        if ($request->filled('availability_status')) {
            $query->where('availability_status', $request->availability_status);
        }
// dd($query->toSql(), $query->getBindings());
        $cars = $query->get();
 if ($cars->isEmpty()) {
    return response()->json(['message' => 'This car is not available'], 404);
}
        return response()->json(['cars' => $cars], 200);
    }
    public function deleteCar($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }
        if ($car->orders()->exists()) {
            return response()->json(['message' => 'Car cannot be deleted as it is linked to active bookings'], 400);
        }

        $car->delete();

        return response()->json(['message' => 'Car deleted successfully'], 200);
    }

}
