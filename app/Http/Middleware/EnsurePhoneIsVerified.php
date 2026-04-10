<?php

namespace App\Http\Middleware;

use App\Enums\OtpType;
use App\Exceptions\OtpException;
use App\Services\OtpService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is not authenticated, redirect to login
        if (! $user) {
            return redirect()->route('login');
        }

        // Check if phone is already verified
        if ($user->phone_verified_at) {
            return $next($request);
        }

        // Send OTP if not already sent in this session
        if (! session()->has('otp_sent_for_verification')) {
            try {
                $this->otpService->generateAndSend(
                    phone: $user->phone,
                    type: OtpType::PhoneVerification,
                    userId: $user->id
                );
            } catch (OtpException $e) {
                Log::channel('otp')->warning('Middleware OTP send failed: '.$e->getMessage());
            }

            session(['otp_sent_for_verification' => true]);
        }

        // Store the intended URL to redirect after verification
        session(['verification_redirect' => $request->url()]);

        // Store verification context
        session([
            'verification_phone' => $user->phone,
            'verification_type' => OtpType::PhoneVerification,
        ]);

        return redirect()->route('phone.verification.show')
            ->with('must_verify', true);
    }
}
