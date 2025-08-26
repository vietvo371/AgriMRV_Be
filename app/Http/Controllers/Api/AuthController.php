<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\LoginRequest;
class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'phone' => ['required', 'regex:/^[+0-9\-\s]{10,}$/'],
            'dob' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
            'role' => ['required', 'in:farmer,bank,cooperative,verifier,government,buyer'],
            'gps_location' => ['required', 'regex:/^-?\d{1,2}\.\d+,\s*-?\d{1,3}\.\d+$/'],
            'org_name' => ['nullable', 'string'],
        ]);

        [$lat, $lon] = array_map('trim', explode(',', $validated['gps_location']));

        $user = User::create([
            'full_name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['dob'],
            'user_type' => $validated['role'],
            'gps_latitude' => $lat,
            'gps_longitude' => $lon,
            'organization_name' => $validated['org_name'] ?? null,
            'organization_type' => $validated['role'] === 'bank' ? 'bank' : ($validated['role'] === 'cooperative' ? 'cooperative' : null),
        ]);

        $token = $user->createToken('api')->plainTextToken;
        return $this->success([
            'user' => $this->transformUser($user),
            'token' => $token,
        ], 'Registered', 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->identifier)
        ->orWhere('phone', $request->identifier)
        ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['identifier or password is incorrect.'],
            ]);
        }

        $token = $user->createToken('user_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        return $this->success($this->transformUser($request->user()));
    }

    public function checkLogin(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'token' => null], 200);
        }
        // Optional refresh: issue a new token and revoke old
        $request->user()->currentAccessToken()?->delete();
        $newToken = $user->createToken('api')->plainTextToken;
        return response()->json(['status' => true, 'token' => $newToken], 200);
    }

    public function requestOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);
        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return $this->error('User not found', 404);
        }
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->save();
        $user->SendOtpMaill($otp);
        return $this->success(null, 'OTP requested');
    }

    private function transformUser(User $user): array
    {
        $gps = null;
        if (!is_null($user->gps_latitude) && !is_null($user->gps_longitude)) {
            $gps = $user->gps_latitude . ',' . $user->gps_longitude;
        }
        $role = $user->user_type === 'cooperative' ? 'coop' : $user->user_type;
        return [
            'user_id' => $user->id,
            'role' => $role,
            'name' => $user->full_name,
            'dob' => optional($user->date_of_birth)->format('Y-m-d'),
            'phone' => $user->phone,
            'email' => $user->email,
            'gps_location' => $gps,
            'org_name' => $user->organization_name,
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return $this->success(null, 'Logged out');
    }
}
