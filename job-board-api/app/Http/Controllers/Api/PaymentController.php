<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function checkout(StorePaymentRequest $request): JsonResponse
    {
        $application = Application::with(['job', 'candidate'])->find($request->validated()['application_id']);

        if (! $application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found',
            ], 404);
        }

        $user = $request->user();

        if ((int) $application->job->employer_id !== (int) $user->id && ! $user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: You do not own this job',
            ], 403);
        }

        $amount = $request->validated()['payment_type'] === 'pay_per_hire'
            ? (float) config('services.stripe.pay_per_hire_amount', 100)
            : (float) config('services.stripe.unlock_contact_amount', 25);

        $currency = strtolower((string) config('services.stripe.currency', 'usd'));
        $stripeSecret = config('services.stripe.secret');

        if (! $stripeSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured',
            ], 500);
        }

        $stripe = new StripeClient($stripeSecret);
        $successUrl = config('services.stripe.success_url') ?: rtrim(config('app.url'), '/') . '/api/payments/confirm?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = config('services.stripe.cancel_url') ?: rtrim(config('app.url'), '/') . '/api/payments/cancel';

        try {
            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $user->email,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'line_items' => [[
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => (int) round($amount * 100),
                        'product_data' => [
                            'name' => $request->validated()['payment_type'] === 'pay_per_hire'
                                ? 'Pay per hire access'
                                : 'Unlock contact details',
                        ],
                    ],
                ]],
                'metadata' => [
                    'application_id' => (string) $application->id,
                    'payment_type' => $request->validated()['payment_type'],
                ],
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to create Stripe checkout session',
            ], 500);
        }

        $payment = Payment::create([
            'employer_id' => $user->id,
            'application_id' => $application->id,
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'payment_method' => 'stripe',
            'transaction_id' => $session->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stripe checkout session created successfully',
            'data' => [
                'payment' => $payment->load(['application.job', 'employer']),
                'checkout_url' => $session->url,
                'session_id' => $session->id,
            ],
        ], 201);
    }

    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $sessionId = $request->input('session_id');

        $payment = Payment::where('transaction_id', $sessionId)->first();

        if (! $payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        $user = $request->user();

        if ((int) $payment->employer_id !== (int) $user->id && ! $user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $stripeSecret = config('services.stripe.secret');

        if (! $stripeSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured',
            ], 500);
        }

        $stripe = new StripeClient($stripeSecret);
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        $payment->update([
            'status' => $session->payment_status === 'paid' ? 'paid' : 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment verification completed',
            'data' => [
                'payment' => $payment->fresh(['application.job', 'employer']),
                'payment_status' => $session->payment_status,
            ],
        ]);
    }

    public function cancel(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Payment was canceled',
        ], 200);
    }
}
