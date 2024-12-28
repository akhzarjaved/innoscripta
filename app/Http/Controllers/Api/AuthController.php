<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\OTPVerificationRequest;
use App\Http\Requests\Api\PasswordResetRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Notifications\SendOtpToUserNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ValidationErrors",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Validation error message"),
 *     @OA\Property(property="errors", type="object")
 * )
 */

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User Login",
     *     description="Login a user by providing email and password.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="token", type="string", example="token123"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $request->user();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => new UserResource($user)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     description="Register a new user by providing name, email, and password.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="token", type="string", example="token123"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout current user",
     *     description="Logout current logged-in user.",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User Logged out")
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'User Logged out']);
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Forgot password",
     *     description="Send an email with OTP for password reset.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Confirmation OTP has been sent to your email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     *     ),
     *      @OA\Response(
     *          response=429,
     *          description="Too many attempts",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Too Many Attempts")
     *          )
     *      )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        PasswordResetOtp::updateOrCreate([
            'email' => $request->email
        ], [
            'otp' => $otp,
            'expires_at' => $expiresAt
        ]);

        Notification::route('mail', $request->email)->notify(new SendOtpToUserNotification($otp));

        return response()->json(['message' => 'Confirmation OTP has been sent to your email']);
    }

    /**
     * @OA\Post(
     *     path="/api/otp-verification",
     *     summary="OTP verification",
     *     description="Verification of the OTP sent to an email.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="otp", type="numeric", example="123123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP verified.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid or expired OTP.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     *     )
     * )
     */
    public function otpVerification(OTPVerificationRequest $request)
    {
        $account = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$account || $account->expires_at->isPast()) {
            return response()->json(['message' => 'Invalid or expired OTP.'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => 'OTP verified.']);
    }

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Password reset",
     *     description="Password reset against the account.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp", "password","password_confirmation"},
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", example="password"),
     *             @OA\Property(property="otp", type="numeric", example="123123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Account not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     *     )
     * )
     */
    public function resetPassword(PasswordResetRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Account not found.'], Response::HTTP_BAD_REQUEST);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $user->tokens()->delete();

        PasswordResetOtp::where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successfully.']);
    }

}
