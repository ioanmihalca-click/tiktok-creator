<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\UserCredit;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = static::getModel()::create($data);

        // Create initial user credit record if needed
        if (isset($data['free_credits']) && $data['free_credits'] > 0) {
            $user->userCredit()->create([
                'free_credits' => $data['free_credits'],
                'used_free_credits' => 0,
                'credits' => 0,
                'used_credits' => 0,
            ]);

            // Add a transaction record
            $user->creditTransactions()->create([
                'transaction_type' => 'admin_adjustment',
                'amount' => $data['free_credits'],
                'description' => 'Initial free credits allocation'
            ]);
        }

        if (isset($data['paid_credits']) && $data['paid_credits'] > 0) {
            if (!$user->userCredit) {
                $user->userCredit()->create([
                    'free_credits' => 0,
                    'used_free_credits' => 0,
                    'credits' => $data['paid_credits'],
                    'used_credits' => 0,
                ]);
            } else {
                $user->userCredit->update([
                    'credits' => $data['paid_credits'],
                ]);
            }

            // Add a transaction record
            $user->creditTransactions()->create([
                'transaction_type' => 'admin_adjustment',
                'amount' => $data['paid_credits'],
                'description' => 'Initial paid credits allocation'
            ]);
        }

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
