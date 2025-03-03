<?php

namespace App\Services;

use App\Models\User;
use App\Models\CreditPackage;
use App\Services\CreditService;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(User $user, CreditPackage $package)
    {
        // Convertește prețul în număr întreg (fără zecimale)
        $unitAmount = (int) $package->price;

        return Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'ron',
                        'product_data' => [
                            'name' => $package->name,
                            'description' => $package->description
                        ],
                        'unit_amount' => $unitAmount, // Număr întreg (fără zecimale)
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('credits.success', ['session_id' => '{CHECKOUT_SESSION_ID}']),
            'cancel_url' => route('credits.cancel'),
            'metadata' => [
                'user_id' => $user->id,
                'package_id' => $package->id,
                'credits' => $package->credits
            ]
        ]);
    }

    public function handleWebhook($payload, $sigHeader)
    {
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook.secret')
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // Verificăm dacă plata a fost efectuată
                if ($session->payment_status === 'paid') {
                    // Credit the user's account
                    $userId = $session->metadata->user_id;
                    $packageId = $session->metadata->package_id;
                    $credits = $session->metadata->credits;

                    $user = User::find($userId);
                    $package = CreditPackage::find($packageId);

                    if ($user && $package) {
                        app(CreditService::class)->addCredits(
                            $user,
                            $credits,
                            'purchase',
                            $session->payment_intent,
                            "Achiziție {$package->name} - {$package->description}"
                        );
                    }
                }
                break;

            case 'payment_intent.succeeded':
                // Poate fi folosit pentru logare sau alte acțiuni
                break;

            case 'payment_intent.payment_failed':
                // Poate fi folosit pentru a notifica utilizatorul despre eșec
                break;
        }

        return response('Webhook handled', 200);
    }
}
