<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Password;

class AuthenticationController extends Controller
{

    public function register(RegisterRequest $request) {
        $request->validated();
        $userData = [
            'username' => $request->username,
            'email'=> $request->email,
            'password'=>Hash::make($request->password),
        ];

        $user = User::create($userData);
        // $token = $user->createToken('todo_app')->plainTextToken;

        return response([
            'message' => 'Registration successful',
            'user'=>$user,
            // 'token'=> $token,
        ], 201);   
    }

    public function login(LoginRequest $request) {
        $request->validated();
        
        $user = User::whereEmail($request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'incorrect credencials',
            ], 422);
        } else {
            if (!$user->email_verified) {
                return response()->json(['message' => 'Email not verified'], 200);
            }
            $token = $user->createToken('todo_app')->plainTextToken;
            return response([
                'message' => 'success',
                'user'=>$user,
                'token'=> $token,
            ], 200);  
        }
    }

    public function sendOtp(Request $request) {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = rand(100000, 999999);

        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP sent to email.']);
    }
    
    public function verifyOtp(Request $request) {
       $user = User::where('email', $request->email)->first();

       if (!$user || $user->otp !== $request->otp || now()->greaterThan("$user->otp_expres_at")) {
        return response(['message' => 'invalid or expired OTP'], 400);
       }

       $user->email_verified = true;
       $user->otp = null;
       $user->otp_expires_at = null;
       $user->email_verified_at = now();
       $user->save();
       $token = $user->createToken('todo_app')->plainTextToken;

       return response([
        'message' => 'Email verified successfully.',
        'user'=>$user,
        'token' => $token,
        ]);

    }

    public function sendPasswordReset(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(100000, 999999);
        $expiry = Carbon::now()->addMinutes(10);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email'=> $request->email],
            ['token' => $otp, 
            'created_at' => Carbon::now(),
            'expires_at' => $expiry,
            ]
        );

        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP has been sent to your email.']);
    }

    public function resetPassword (Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' =>'required|numeric',
            'password'=>'required|confirmed|min:6',
        ]);

        $reset = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->otp)
                    ->where('expires_at', '>=', Carbon::now())
                    ->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    // Delete the password reset entry
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json(['message' => 'Password has been reset successfully.']);

    }


    // public function forgotPassword(Request $request) {
    //     $request->validate(['email' => 'required|email']);
    
    //     $status = Password::sendResetLink(
    //         $request->only('email')
    //     );
    
    //     return $status === Password::RESET_LINK_SENT
    //                 ? response()->json(['message' => __($status)], 200)
    //                 : response()->json(['message' => __($status)], 400);
    // }

    // public function resetPassword(Request $request) {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'token' => 'required',
    //         'password' => 'required|min:8|confirmed',
    //     ]);
    
    //     $status = Password::reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function ($user, $password) {
    //             $user->forceFill([
    //                 'password' => Hash::make($password),
    //             ])->save();
    //         }
    //     );
    
    //     return $status === Password::PASSWORD_RESET
    //                 ? response()->json(['message' => __($status)], 200)
    //                 : response()->json(['message' => __($status)], 400);
    // }
    
}
