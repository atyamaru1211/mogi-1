<?php

/*namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('Stripe Webhook Received');
    Log::info($request->all());

    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $secret = env('STRIPE_WEBHOOK_SECRET');

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, $secret
        );
    } catch (\UnexpectedValueException $e) {
        Log::error('Invalid payload: ' . $e->getMessage());
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        Log::error('Invalid signature: ' . $e->getMessage());
        return response('Invalid signature', 400);
    }

    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;
        Log::info('Checkout Session Completed:', ['id' => $session->id]);
        // ここで購入処理などを行う
    } elseif ($event->type === 'payment_intent.succeeded') {
        $paymentIntent = $event->data->object;
        Log::info('Payment Intent Succeeded:', ['id' => $paymentIntent->id]);
        // ここで購入処理などを行う (コンビニ払いなど)
    }

    return new Response('Webhook received', 200);
    }
}*/
