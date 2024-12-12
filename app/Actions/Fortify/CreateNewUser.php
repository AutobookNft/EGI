<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Models\Collection;
use App\Traits\HasCreateDefaultTeamWallets;
use App\Traits\HasUtilitys;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Str;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use HasCreateDefaultTeamWallets;
    use HasUtilitys;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Log::channel('florenceegi')->info('Classe: CreateNewUser Metodo create: Action: INIZIO', ['input' => $input]);

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

            Log::channel('florenceegi')->info('Classe: CreateNewUser Metodo validateInput: Action: VALIDAZIONE OK');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('florenceegi')->error('Errore di validazione', ['errors' => $e->errors()]);
            throw $e;
        }
    }

    private function generateWalletDetails(): array
    {
        $wallet_address = $this->generateFakeAlgorandAddress();
        Log::channel('florenceegi')->info('Classe: CreateNewUser Metodo generateWalletDetails: Action: GENERATO WALLET ADDRESS', ['wallet_address' => $wallet_address]);

        $wallet_balance = config('app.virtual_wallet_balance');
        Log::channel('florenceegi')->info('Classe: CreateNewUser Metodo generateWalletDetails: Action: GENERATO WALLET BALANCE', ['wallet_balance' => $wallet_balance]);

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
                Log::channel('florenceegi')->info('User creato con successo', ['user_id' => $user->id]);

                // Crea il team e la collection predefinita utilizzando tap
                tap($this->createTeam($user), function ($team) use ($user) {
                    Log::channel('florenceegi')->info('Team creato con successo', ['team_id' => $team->id, 'user_id' => $user->id]);
                    $this->createDefaultCollection($team, $user);
                });
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): Team
    {
        $team = $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'epp_id' => config('app.epp_id'),
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));

        $team->users()->attach($user->id, ['role' => 'creator']);

        return $team;
    }


    /**
     * Create a default collection for the user's team and associarla alla tabella pivot team_collection.
     */
    protected function createDefaultCollection(Team $team, User $user): void
    {
        tap(Collection::create([
            'user_id' => $user->id,
            'epp_id' => config('app.epp_id'),
            'collection_name' => explode(' ', $user->name, 2)[0] . "'s collection",
            'description' => __('collection.this_is_default_collection_of_the_team') ?? 'This is the default collection of the team',
            'creator_id' => $user->id,
            'type' => __('collection.type_image') ?? 'image',
            'position' => 1,
            'EGI_number' => 1,
            'floor_price' => 0.0,
            'show' => true,
            'personal_team' => true,
        ]), function (Collection $collection) use ($team, $user) {
            Log::channel('florenceegi')->info('Collection creata con successo', ['collection_id' => $collection->id]);

            // Associa la collection al team nella tabella pivot team_collection
            $team->collections()->attach($collection->id);

            Log::channel('florenceegi')->info('Collection associata al team nella tabella pivot team_collection', [
                'team_id' => $team->id,
                'collection_id' => $collection->id,
            ]);

            // Associa i wallet predefiniti al team
            $this->attachDefaultWallets($team, $user);

            // Assegna il ruolo di creator all'utente
            $this->assignCreatorRole($user->id);
        });
    }

    public function assignCreatorRole(int $userId)
    {
        // Trova l'utente con l'ID specificato
        $user = User::find($userId);

        // Controlla se l'utente esiste
        if (!$user) {
            session()->flash('error', 'Utente non trovato.');
            return;
        }

        // Controlla se il ruolo 'creator' esiste, altrimenti lo crea
        $creatorRole = Role::firstOrCreate(['name' => 'creator']);

        // Assegna il ruolo 'creator' all'utente
        if (!$user->hasRole('creator')) {
            $user->assignRole($creatorRole);
            session()->flash('success', 'Ruolo di creator assegnato con successo.');
        } else {
            session()->flash('info', 'L\'utente ha già il ruolo di creator.');
        }
    }

    /**
     * Attach default wallets to the team.
     */
    protected function attachDefaultWallets(Team $team, User $user): void
    {

        $wallet_natan = config('app.natan_wallet_address');

        $wallet_creator = $user->wallet; // Wallet dell'utente appena creato
        $wallet_epp = User::find(config('app.epp_id'))->wallet ?? 'WalletEPP'; // Wallet EPP

        $natan_royalty_mint = config('app.natan_royalty_mint'); // Royalty mint di Natan
        $natan_royalty_rebind = config('app.natan_royalty_rebind'); // Royalty rebind di Natan

        $epp_royalty_mint = config('app.mediator_royalty_mint'); // Royalty mint di EPP
        $epp_royalty_rebind = config('app.mediator_royalty_rebind'); // Royalty rebind di EPP

        $creator_royalty_mint = config('app.creator_royalty_mint'); // Royalty mint del creatore
        $creator_royalty_rebind = config('app.creator_royalty_rebind'); // Royalty rebind del creatore

        $defaultWallets = [
            ['user_id' => $user->id, 'user_role' => 'Creator', 'wallet' => $wallet_creator, 'royalty_mint' => $creator_royalty_mint, 'royalty_rebind' => $creator_royalty_rebind], // ID 0 mappato a Creator
            ['user_id' => config('app.natan_id'), 'user_role' => 'Natan', 'wallet' => $wallet_natan, 'royalty_mint' => $natan_royalty_mint, 'royalty_rebind' => $natan_royalty_rebind], // ID 1 mappato a Natan
            ['user_id' => config('app.epp_id'), 'user_role' => 'EPP', 'wallet' => $wallet_epp, 'royalty_mint' => $epp_royalty_mint, 'royalty_rebind' => $epp_royalty_rebind],     // ID 2 mappato a EPP
        ];

        foreach ($defaultWallets as $wallet) {
            $team->wallets()->create($wallet);
        }
    }
}

