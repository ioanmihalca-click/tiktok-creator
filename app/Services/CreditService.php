<?php

namespace App\Services;

use App\Models\User;
use App\Models\CreditTransaction;
use Illuminate\Support\Facades\DB;
use App\Services\AI\NarrationService;
use Laravel\Cashier\Exceptions\IncompletePayment;

class CreditService
{
    public function addCredits(User $user, int $credits, string $source = 'purchase', ?string $paymentId = null, ?string $description = null)
    {
        DB::beginTransaction();

        try {
            if (!$user->userCredit) {
                $user->userCredit()->create([
                    'credits' => $credits,
                    'free_credits' => 3 // Initial free credits
                ]);
            } else {
                $user->userCredit->increment('credits', $credits);
            }

            // Folosește relația pentru a crea tranzacția
            $user->creditTransactions()->create([
                'transaction_type' => $source,
                'amount' => $credits,
                'payment_id' => $paymentId, // Poate fi null pentru tranzacții non-plată
                'description' => $description ?? "Added {$credits} credits via {$source}",
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return false;
        }
    }
    public function checkCreditType(User $user)
    {
        if (!$user->userCredit) {
            $user->userCredit()->create([
                'free_credits' => 3
            ]);
        }

        if ($user->userCredit->available_free_credits > 0) {
            return 'free';
        } elseif ($user->userCredit->available_credits > 0) {
            return 'paid';
        }

        return false;
    }

    public function getEnvironmentType(User $user)
    {
        $creditType = $this->checkCreditType($user);

        if ($creditType === 'free') {
            return 'sandbox';
        } elseif ($creditType === 'paid') {
            return 'production';
        }

        return 'sandbox'; // Default to sandbox if no credits
    }

    public function shouldHaveWatermark(User $user)
    {
        $creditType = $this->checkCreditType($user);
        return $creditType === 'free';
    }

    // Metodă pentru a determina vocile disponibile
    public function getAvailableVoices(User $user, NarrationService $narrationService)
    {
        $creditType = $this->checkCreditType($user);

        if ($creditType === 'paid') {
            return $narrationService->getAvailableVoices(true); // Include vocile premium
        } else {
            return $narrationService->getAvailableVoices(false); // Doar vocile gratuite
        }
    }
}
