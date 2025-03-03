<?php

namespace App\Services;

use App\Models\User;
use App\Models\CreditPackage;
use App\Models\CreditTransaction;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function addCredits(User $user, int $credits, string $source = 'purchase', string $paymentId = null, string $description = null)
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

            $user->creditTransactions()->create([
                'transaction_type' => $source,
                'amount' => $credits,
                'payment_id' => $paymentId,
                'description' => $description ?? "Added {$credits} credits via {$source}"
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
}
