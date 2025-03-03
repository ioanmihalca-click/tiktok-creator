<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        // Handle credit updates
        if (isset($data['free_credits']) && $data['free_credits'] > 0) {
            if (!$record->userCredit) {
                $record->userCredit()->create([
                    'free_credits' => $data['free_credits'],
                    'used_free_credits' => 0,
                    'credits' => 0,
                    'used_credits' => 0,
                ]);

                // Add a transaction record
                $record->creditTransactions()->create([
                    'transaction_type' => 'admin_adjustment',
                    'amount' => $data['free_credits'],
                    'description' => 'Admin added ' . $data['free_credits'] . ' free credits'
                ]);
            } else {
                $originalFreeCredits = $record->userCredit->free_credits;
                $record->userCredit->increment('free_credits', $data['free_credits']);

                // Only create a transaction if credits were actually added
                if ($data['free_credits'] > 0) {
                    $record->creditTransactions()->create([
                        'transaction_type' => 'admin_adjustment',
                        'amount' => $data['free_credits'],
                        'description' => 'Admin added ' . $data['free_credits'] . ' free credits'
                    ]);
                }
            }
        }

        if (isset($data['paid_credits']) && $data['paid_credits'] > 0) {
            if (!$record->userCredit) {
                $record->userCredit()->create([
                    'free_credits' => 0,
                    'used_free_credits' => 0,
                    'credits' => $data['paid_credits'],
                    'used_credits' => 0,
                ]);

                // Add a transaction record
                $record->creditTransactions()->create([
                    'transaction_type' => 'admin_adjustment',
                    'amount' => $data['paid_credits'],
                    'description' => 'Admin added ' . $data['paid_credits'] . ' paid credits'
                ]);
            } else {
                $originalPaidCredits = $record->userCredit->credits;
                $record->userCredit->increment('credits', $data['paid_credits']);

                // Only create a transaction if credits were actually added
                if ($data['paid_credits'] > 0) {
                    $record->creditTransactions()->create([
                        'transaction_type' => 'admin_adjustment',
                        'amount' => $data['paid_credits'],
                        'description' => 'Admin added ' . $data['paid_credits'] . ' paid credits'
                    ]);
                }
            }
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
