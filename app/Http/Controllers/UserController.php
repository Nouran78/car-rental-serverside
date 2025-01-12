<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Fetch all users
    public function index()
    {

        $users = User::all();
        return response()->json($users);
    }

    // Fetch a single user by ID
    public function getUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    // Register a new user
    public function register(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email', // Ensure uniqueness by specifying the column
            'password' => 'required|min:6|confirmed',
            'role' => 'nullable|in:user,admin', // Optional role validation
        ]);

        try {
            // Create a new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password
                'role' => $request->role ?? 'user', // Default to 'user' role if none provided
            ]);

            // Generate an authentication token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            $cookie = cookie('token', $token, 60 * 24);

            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
                'token' => $token,
            ])->withCookie($cookie);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // User login
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $credentials = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? ['email' => $request->login, 'password' => $request->password]
            : ['name' => $request->login, 'password' => $request->password];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken($user->role . '_token')->plainTextToken;
        $cookie = cookie('token', $token, 60 * 24);

        return response()->json([
            'message' => ucfirst($user->role) . ' logged in successfully',
            'user' => $user,
            'token' => $token,
        ])->withCookie($cookie);
    }

    // User logout
    public function logout()
    {
        Auth::user()->tokens()->delete();
        $cookie = Cookie::forget('token');
        return response()->json(['message' => 'Logged out successfully'])->withCookie($cookie);
    }

    // Get the authenticated user
    public function user()
    {
        return response()->json(['user' => Auth::user()]);
    }

    // Update the authenticated user's profile
    public function updateOwnUser(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'sometimes|required|min:3',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'old_password' => 'required',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Old password is incorrect'], 403);
        }

        $user->update($request->only(['name', 'email']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    // Update another user by admin
    public function updateUser(Request $request, User $user)
    {
        $this->authorize('admin-only');

        $request->validate([
            'name' => 'sometimes|required|min:3',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'nullable|in:user,admin',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    // Delete a user by admin
    public function deleteUser(User $user)
    {
        $this->authorize('admin-only'); // Only admins can delete users

        if (Auth::id() === $user->id) {
            return response()->json(['message' => 'You cannot delete your own account'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
