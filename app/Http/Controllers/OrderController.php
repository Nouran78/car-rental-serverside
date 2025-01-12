<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function createOrder(Request $request)
    {
        try {
            $request->validate([
                'car_id' => 'required|exists:cars,id',
                'start_date' => 'required|date|date_format:Y-m-d H:i:s|after_or_equal:' . now()->format('Y-m-d H:i:s'),
                'end_date' => 'required|date|date_format:Y-m-d H:i:s|after:start_date',
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $car = Car::find($request->car_id);
            if (!$car) {
                throw new \Exception('Car not found');
            }

            $existingBookings = Order::where('car_id', $car->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                          ->orWhere(function ($query) use ($request) {
                              $query->where('start_date', '<=', $request->start_date)
                                    ->where('end_date', '>=', $request->end_date);
                          });
                })
                ->exists();

            if ($existingBookings) {
                throw new \Exception('The car is not available for the selected dates');
            }

            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $interval = $startDate->diff($endDate);
            $days = $interval->days;

            if ($days <= 0) {
                throw new \Exception('Invalid rental period');
            }

            $totalPrice = $car->price_per_day * $days;

            $discountMessage = '';
            if ($days > 7) {
                $totalPrice = $totalPrice * 0.9;
                $discountMessage = 'You have 10% off for renting for more than 7 days.';
            }

            $order = Order::create([
                'user_id' => $user->id,
                'car_id' => $car->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_price' => $totalPrice,
                'payment_status' => 'unpaid',
            ]);

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order,
                'discount_message' => $discountMessage,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }



    public function updateOrderPaymentStatus(Request $request, $orderId)
    {
        try {
            $request->validate([
                'payment_status' => 'required|in:paid',
            ]);

            $order = Order::find($orderId);
            if (!$order) {
                throw new \Exception('Order not found');
            }

            if ($order->payment_status === 'paid') {
                throw new \Exception('The order is already paid');
            }

           

            $order->payment_status = 'paid';
            $order->save();

            return response()->json([
                'message' => 'Payment status updated successfully',
                'order' => $order,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }



    public function listOrders()
    {
        try {

            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }


            $orders = Order::with('car')
                ->where('user_id', $user->id)
                ->get();

            return response()->json([
                'orders' => $orders,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
