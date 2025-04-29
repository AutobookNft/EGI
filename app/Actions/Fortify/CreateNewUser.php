<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Collection;
use App\Traits\HasCreateDefaultCollectionWallets;
use App\Traits\HasUtilitys;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use HasCreateDefaultCollectionWallets;
    use HasUtilitys;


    /**
     * Crea un nuovo utente registrato.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Log::channel('florenceegi')->info('Classe: CreateNewUser Metodo create: INIZIO', ['input' => $input]);

        // Validazione dell'input
        $this->validateInput($input);

        // Generazione dei dettagli del wallet
        [$wallet_address, $wallet_balance] = $this->generateWalletDetails();

        // Creazione dell'utente e delle risorse collegate
        return $this->handleUserCreation($input, $wallet_address, $wallet_balance);
    }

    private function validateInput(array $input): void
    {
        try {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
            ])->validate();

            Log::channel('florenceegi')->info('Classe: CreateNewUser Metodo validateInput: VALIDAZIONE OK');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('florenceegi')->error('Errore di validazione', ['errors' => $e->errors()]);
            throw $e;
        }
    }

    private function generateWalletDetails(): array
    {
        $wallet_address = $this->generateFakeAlgorandAddress();
        Log::channel('florenceegi')->info('Generato wallet address', ['wallet_address' => $wallet_address]);

        $wallet_balance = config('app.virtual_wallet_balance');
        Log::channel('florenceegi')->info('Generato wallet balance', ['wallet_balance' => $wallet_balance]);

        return [$wallet_address, $wallet_balance];
    }

    private function handleUserCreation(array $input, string $wallet_address, float $wallet_balance): User
    {
        return DB::transaction(function () use ($input, $wallet_address, $wallet_balance) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'wallet' => $wallet_address,
                'wallet_balance' => $wallet_balance,
                'language' => app()->getLocale(),
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                Log::channel('florenceegi')->info('Utente creato con successo', ['user_id' => $user->id]);

                // 1) creo la collection default e la recupero
                $collection = $this->createDefaultCollection($user);

                // 2) assegno current_collection_id
                $user->current_collection_id = $collection->id;
                $user->save();

            });
        });
    }

    /**
     * Crea una collection predefinita per l'utente.
     */
    protected function createDefaultCollection(User $user): Collection
    {
        return tap(Collection::create([
            'user_id'         => $user->id,
            'epp_id'          => config('app.epp_id'),
            'collection_name' => explode(' ', $user->name, 2)[0] . "'s Collection",
            'description'     => __('collection.default_description'),
            'creator_id'      => $user->id,
            'type'            => 'standard',
            'position'        => 1,
            'EGI_number'      => 1,
            'floor_price'     => 0.0,
            'is_published'    => false,
        ]), function (Collection $collection) use ($user) {
            Log::channel('florenceegi')->info('Collection creata con successo', ['collection_id' => $collection->id]);

            // Pivot user–collection
            $collection->users()->attach($user->id, ['role' => 'creator']);

            // Wallet default
            $this->attachDefaultWallets($collection, $user);

            // Ruolo creator
            $this->assignCreatorRole($user->id);
        });
    }

    public function assignCreatorRole(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            Log::channel('florenceegi')->error('Utente non trovato durante l\'assegnazione del ruolo', ['user_id' => $userId]);
            return;
        }

        $creatorRole = Role::firstOrCreate(['name' => 'creator']);

        if (!$user->hasRole('creator')) {
            $user->assignRole($creatorRole);
            Log::channel('florenceegi')->info('Assegnato ruolo creator all\'utente', ['user_id' => $userId]);
        }
    }

    /**
     * Crea i wallet predefiniti per la collection.
     */
    protected function attachDefaultWallets(Collection $collection, User $user): void
    {
        $defaultWallets = [
            ['user_id' => $user->id, 'wallet' => $user->wallet, 'royalty_mint' => 50.0, 'royalty_rebind' => 10.0], // Wallet Creator
            ['user_id' => config('app.natan_id'), 'wallet' => config('app.natan_wallet_address'), 'royalty_mint' => 25.0, 'royalty_rebind' => 5.0], // Wallet Natan
            ['user_id' => config('app.epp_id'), 'wallet' => config('app.epp_wallet_address'), 'royalty_mint' => 25.0, 'royalty_rebind' => 5.0], // Wallet EPP
        ];

        foreach ($defaultWallets as $wallet) {
            $collection->wallets()->create($wallet);
        }

        Log::channel('florenceegi')->info('Wallet predefiniti associati alla collection', ['collection_id' => $collection->id]);
    }
}
