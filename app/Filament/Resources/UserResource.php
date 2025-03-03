<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\UserCredit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),
                        Forms\Components\DateTimePicker::make('email_verified_at'),
                    ]),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('Credits Information')
                            ->content(fn($record) => $record && $record->exists ? 'Manage credits for user' : 'Save user first to manage credits'),
                        Forms\Components\TextInput::make('free_credits')
                            ->label('Free Credits')
                            ->numeric()
                            ->visible(fn($record) => $record && $record->exists)
                            ->helperText('Add additional free credits to user'),
                        Forms\Components\TextInput::make('paid_credits')
                            ->label('Paid Credits')
                            ->numeric()
                            ->visible(fn($record) => $record && $record->exists)
                            ->helperText('Add additional paid credits to user'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('userCredit.free_credits')
                    ->label('Free Credits')
                    ->sortable()
                    ->getStateUsing(function (User $record) {
                        return $record->userCredit ? $record->userCredit->free_credits - $record->userCredit->used_free_credits : 0;
                    })
                    ->description(fn(User $record) => $record->userCredit
                        ? "Total: {$record->userCredit->free_credits} - Used: {$record->userCredit->used_free_credits}"
                        : "No credits"),
                Tables\Columns\TextColumn::make('userCredit.credits')
                    ->label('Paid Credits')
                    ->sortable()
                    ->getStateUsing(function (User $record) {
                        return $record->userCredit ? $record->userCredit->credits - $record->userCredit->used_credits : 0;
                    })
                    ->description(fn(User $record) => $record->userCredit
                        ? "Total: {$record->userCredit->credits} - Used: {$record->userCredit->used_credits}"
                        : "No credits"),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('add_free_credits')
                    ->label('Add Free Credits')
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->form([
                        Forms\Components\TextInput::make('free_credits')
                            ->label('Free Credits')
                            ->numeric()
                            ->required()
                            ->default(3)
                            ->minValue(1)
                    ])
                    ->action(function (User $record, array $data) {
                        if (!$record->userCredit) {
                            $record->userCredit()->create([
                                'free_credits' => $data['free_credits'],
                                'used_free_credits' => 0,
                                'credits' => 0,
                                'used_credits' => 0,
                            ]);
                        } else {
                            $record->userCredit->increment('free_credits', $data['free_credits']);
                        }

                        // Add a transaction record
                        $record->creditTransactions()->create([
                            'transaction_type' => 'admin_adjustment',
                            'amount' => $data['free_credits'],
                            'description' => 'Admin added ' . $data['free_credits'] . ' free credits'
                        ]);
                    }),
                Tables\Actions\Action::make('add_paid_credits')
                    ->label('Add Paid Credits')
                    ->color('primary')
                    ->icon('heroicon-o-banknotes')
                    ->form([
                        Forms\Components\TextInput::make('paid_credits')
                            ->label('Paid Credits')
                            ->numeric()
                            ->required()
                            ->default(5)
                            ->minValue(1)
                    ])
                    ->action(function (User $record, array $data) {
                        if (!$record->userCredit) {
                            $record->userCredit()->create([
                                'free_credits' => 0,
                                'used_free_credits' => 0,
                                'credits' => $data['paid_credits'],
                                'used_credits' => 0,
                            ]);
                        } else {
                            $record->userCredit->increment('credits', $data['paid_credits']);
                        }

                        // Add a transaction record
                        $record->creditTransactions()->create([
                            'transaction_type' => 'admin_adjustment',
                            'amount' => $data['paid_credits'],
                            'description' => 'Admin added ' . $data['paid_credits'] . ' paid credits'
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('add_free_credits_bulk')
                    ->label('Add Free Credits')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('free_credits')
                            ->label('Free Credits')
                            ->numeric()
                            ->required()
                            ->default(3)
                            ->minValue(1)
                    ])
                    ->action(function (array $records, array $data) {
                        foreach ($records as $record) {
                            if (!$record->userCredit) {
                                $record->userCredit()->create([
                                    'free_credits' => $data['free_credits'],
                                    'used_free_credits' => 0,
                                    'credits' => 0,
                                    'used_credits' => 0,
                                ]);
                            } else {
                                $record->userCredit->increment('free_credits', $data['free_credits']);
                            }

                            // Add a transaction record
                            $record->creditTransactions()->create([
                                'transaction_type' => 'admin_adjustment',
                                'amount' => $data['free_credits'],
                                'description' => 'Admin added ' . $data['free_credits'] . ' free credits (bulk action)'
                            ]);
                        }
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
