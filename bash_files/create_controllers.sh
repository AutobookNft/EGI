#!/bin/bash

# Definizione dei controller da creare
CONTROLLERS=(
    "StatisticsController:index"
    "NotificationsController:index,markAsRead"
    "PersonalDataController:account,bio,updateAccount,updateBio"
    "GdprController:consents,deleteAccount,updateConsents,downloadData,destroyAccount"
    "DocumentationController:index"
    "AdminController:rolesIndex,assignRoleForm,assignRole,assignPermissionsForm,assignPermissions"
)

# Crea la directory dei controller se non esiste
mkdir -p app/Http/Controllers

for item in "${CONTROLLERS[@]}"; do
    # Splitta il nome del controller e i suoi metodi
    IFS=":" read -r controller_name methods <<< "$item"

    # Se il file già esiste, salta
    if [ -f "app/Http/Controllers/${controller_name}.php" ]; then
        echo "⚠️ Il controller ${controller_name} esiste già. Saltato."
        continue
    fi

    echo "🔨 Creazione del controller ${controller_name}..."

    # Prepara un array di metodi
    IFS="," read -ra methods_array <<< "$methods"

    # Inizia a costruire il contenuto del file
    content="<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Facades\UltraError;

/**
 * @Oracode Controller: ${controller_name}
 * 🎯 Purpose: Handle ${controller_name%Controller} related operations
 * 🧱 Core Logic: Manages views and actions for ${controller_name%Controller} section
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date $(date +'%Y-%m-%d')
 */
class ${controller_name} extends Controller
{
    /**
     * Logger instance
     *
     * @var UltraLogManager
     */
    protected UltraLogManager \$logger;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager \$logger
     */
    public function __construct(UltraLogManager \$logger)
    {
        \$this->logger = \$logger;
    }
"

    # Aggiungi ogni metodo al controller
    for method in "${methods_array[@]}"; do
        docblock=""
        method_signature=""
        method_body=""

        case "$method" in
            "index")
                docblock="
    /**
     * Display the ${controller_name%Controller} dashboard
     *
     * @return View
     */
"
                method_signature="public function index(): View"
                method_body="
    {
        \$this->logger->info('Accessing ${controller_name%Controller} dashboard');

        return view('${controller_name%Controller}.index');
    }"
                ;;

            "account")
                docblock="
    /**
     * Display account data management page
     *
     * @return View
     */
"
                method_signature="public function account(): View"
                method_body="
    {
        \$this->logger->info('Accessing account data management');

        return view('personal-data.account', [
            'user' => auth()->user()
        ]);
    }"
                ;;

            "bio")
                docblock="
    /**
     * Display bio/profile management page
     *
     * @return View
     */
"
                method_signature="public function bio(): View"
                method_body="
    {
        \$this->logger->info('Accessing bio/profile management');

        return view('personal-data.bio', [
            'user' => auth()->user()
        ]);
    }"
                ;;

            "updateAccount")
                docblock="
    /**
     * Update user account data
     *
     * @param Request \$request
     * @return RedirectResponse
     */
"
                method_signature="public function updateAccount(Request \$request): RedirectResponse"
                method_body="
    {
        try {
            \$validated = \$request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
                // Altri campi...
            ]);

            \$user = auth()->user();
            \$user->update(\$validated);

            \$this->logger->info('Account data updated', [
                'user_id' => \$user->id
            ]);

            return redirect()->route('personal-data.account')
                ->with('success', __('personal_data.account_updated'));

        } catch (\Exception \$e) {
            return UltraError::handle('ACCOUNT_UPDATE_FAILED', [
                'error' => \$e->getMessage()
            ], \$e)->with('error', __('personal_data.update_failed'));
        }
    }"
                ;;

            "updateBio")
                docblock="
    /**
     * Update user bio/profile data
     *
     * @param Request \$request
     * @return RedirectResponse
     */
"
                method_signature="public function updateBio(Request \$request): RedirectResponse"
                method_body="
    {
        try {
            \$validated = \$request->validate([
                'bio' => 'nullable|string|max:1000',
                'social_links' => 'nullable|array',
                // Altri campi...
            ]);

            \$user = auth()->user();
            \$user->profile()->update(\$validated);

            \$this->logger->info('Bio/profile updated', [
                'user_id' => \$user->id
            ]);

            return redirect()->route('personal-data.bio')
                ->with('success', __('personal_data.bio_updated'));

        } catch (\Exception \$e) {
            return UltraError::handle('BIO_UPDATE_FAILED', [
                'error' => \$e->getMessage()
            ], \$e)->with('error', __('personal_data.update_failed'));
        }
    }"
                ;;

            "markAsRead")
                docblock="
    /**
     * Mark notification as read
     *
     * @param Request \$request
     * @param string \$notification Notification ID
     * @return RedirectResponse
     */
"
                method_signature="public function markAsRead(Request \$request, string \$notification): RedirectResponse"
                method_body="
    {
        try {
            \$notification = auth()->user()->notifications()->findOrFail(\$notification);
            \$notification->markAsRead();

            \$this->logger->info('Notification marked as read', [
                'notification_id' => \$notification->id
            ]);

            return redirect()->route('notifications.index')
                ->with('success', __('notifications.marked_as_read'));

        } catch (\Exception \$e) {
            return UltraError::handle('NOTIFICATION_MARK_READ_FAILED', [
                'error' => \$e->getMessage()
            ], \$e)->with('error', __('notifications.action_failed'));
        }
    }"
                ;;

            "consents")
                docblock="
    /**
     * Display GDPR consents management page
     *
     * @return View
     */
"
                method_signature="public function consents(): View"
                method_body="
    {
        \$this->logger->info('Accessing GDPR consents management');

        \$user = auth()->user();
        \$consents = \$user->consents()->get();

        return view('gdpr.consents', [
            'user' => \$user,
            'consents' => \$consents
        ]);
    }"
                ;;

            "deleteAccount")
                docblock="
    /**
     * Display account deletion page
     *
     * @return View
     */
"
                method_signature="public function deleteAccount(): View"
                method_body="
    {
        \$this->logger->info('Accessing account deletion page', [
            'user_id' => auth()->id()
        ]);

        return view('gdpr.delete-account');
    }"
                ;;

            "updateConsents")
                docblock="
    /**
     * Update user consents
     *
     * @param Request \$request
     * @return RedirectResponse
     */
"
                method_signature="public function updateConsents(Request \$request): RedirectResponse"
                method_body="
    {
        try {
            \$validated = \$request->validate([
                'consents' => 'required|array',
                'consents.*' => 'boolean',
            ]);

            \$user = auth()->user();
            \$user->updateConsents(\$validated['consents']);

            \$this->logger->info('User consents updated', [
                'user_id' => \$user->id
            ]);

            return redirect()->route('gdpr.consents')
                ->with('success', __('gdpr.consents_updated'));

        } catch (\Exception \$e) {
            return UltraError::handle('CONSENTS_UPDATE_FAILED', [
                'error' => \$e->getMessage()
            ], \$e)->with('error', __('gdpr.update_failed'));
        }
    }"
                ;;

            "downloadData")
                docblock="
    /**
     * Download user personal data
     *
     * @param Request \$request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
"
                method_signature="public function downloadData(Request \$request)"
                method_body="
    {
        try {
            \$user = auth()->user();

            \$this->logger->info('User downloading personal data', [
                'user_id' => \$user->id
            ]);

            // Genera e restituisci i dati personali
            return (new \App\Services\GdprService())->generateUserDataExport(\$user);

        } catch (\Exception \$e) {
            return UltraError::handle('DATA_DOWNLOAD_FAILED', [
                'error' => \$e->getMessage()
            ], \$e)->with('error', __('gdpr.download_failed'));
        }
    }"
                ;;

            "destroyAccount")
                docblock="
    /**
     * Permanently delete user account
     *
     * @param Request \$request
     * @return RedirectResponse
     */
"
                method_signature="public function destroyAccount(Request \$request): RedirectResponse"
                method_body="
    {
        try {
            \$request->validate([
                'password' => 'required|current_password',
                'confirmation' => 'required|in:DELETE'
            ]);

            \$user = auth()->user();
            \$userId = \$user->id;

            \$this->logger->warning('Account deletion requested and confirmed', [
                'user_id' => \$userId
            ]);

            // Esegui la cancellazione dell'account
            \$user->delete();

            // Termina la sessione
            auth()->logout();
            \$request->session()->invalidate();
            \$request->session()->regenerateToken();

            return redirect('/')->with('account_deleted', true);

        } catch (\Exception \$e) {
            return UltraError::handle('ACCOUNT_DELETION_FAILED', [
                'error' => \$e->getMessage()
            ], \$e)->with('error', __('gdpr.deletion_failed'));
        }
    }"
                ;;

            "rolesIndex")
                docblock="
    /**
     * Display roles and permissions management page
     *
     * @return View
     */
"
                method_signature="public function rolesIndex(): View"
                method_body="
    {
        \$this->logger->info('Accessing roles and permissions management');

        \$roles = \App\Models\Role::with('permissions')->get();
        \$permissions = \App\Models\Permission::all();

        return view('admin.roles.index', [
            'roles' => \$roles,
            'permissions' => \$permissions
        ]);
    }"
                ;;

            "assignRoleForm")
                docblock="
    /**
     * Display the form to assign roles to users
     *
     * @return View
     */
"
                method_signature="public function assignRoleForm(): View"
                method_body="
    {
        \$this->logger->info('Accessing assign role form');

        \$users = \App\Models\User::all();
        \$roles = \App\Models\Role::all();

        return view('admin.assign.role', [
            'users' => \$users,
            'roles' => \$roles
        ]);
    }"
                ;;

            "assignRole")
                docblock="
    /**
     * Assign roles to a user
     *
     * @param Request \$request
     * @return RedirectResponse
     */
"
                method_signature="public function assignRole(Request \$request): RedirectResponse"
                method_body="
    {
        try {
            \$validated = \$request->validate([
                'user_id' => 'required|exists:users,id',
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            \$user = \App\Models\User::findOrFail(\$validated['user_id']);
            \$user->syncRoles(\$validated['roles']);

            \$this->logger->info('Roles assigned to user', [
                'user_id' => \$user->id,
                'roles' => \$validated['roles']
            ]);

            return redirect()->route('admin.assign.role.form')
                ->with('success', __('admin.roles_assigned'));

        } catch (\Exception \$e) {
            return UltraError::handle('ROLE_ASSIGNMENT_FAILED', [
                'error' => \$e->getMessage()
            ], \$e)->with('error', __('admin.role_assignment_failed'));
        }
    }"
                ;;

            "assignPermissionsForm")
                docblock="
    /**
     * Display the form to assign permissions to roles
     *
     * @return View
     */
"
                method_signature="public function assignPermissionsForm(): View"
                method_body="
    {
        \$this->logger->info('Accessing assign permissions form');

        \$roles = \App\Models\Role::all();
        \$permissions = \App\Models\Permission::all();

        return view('admin.assign.permissions', [
            'roles' => \$roles,
            'permissions' => \$permissions
        ]);
    }"
                ;;

            "assignPermissions")
                docblock="
    /**
     * Assign permissions to a role
     *
     * @param Request \$request
     * @return RedirectResponse
     */
"
                method_signature="public function assignPermissions(Request \$request): RedirectResponse"
                method_body="
    {
        try {
            \$validated = \$request->validate([
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            \$role = \App\Models\Role::findOrFail(\$validated['role_id']);
            \$role->syncPermissions(\$validated['permissions']);

            \$this->logger->info('Permissions assigned to role', [
                'role_id' => \$role->id,
                'permissions' => \$validated['permissions']
            ]);

            return redirect()->route('admin.assign.permissions.form')
                ->with('success', __('admin.permissions_assigned'));

        } catch (\Exception \$e) {
            return UltraError::handle('PERMISSION_ASSIGNMENT_FAILED', [
                'error' => \$e->getMessage()
            ], \$e)->with('error', __('admin.permission_assignment_failed'));
        }
    }"
                ;;

            *)
                docblock="
    /**
     * ${method} method
     *
     * @return View
     */
"
                method_signature="public function ${method}()"
                method_body="
    {
        \$this->logger->info('Accessing ${method}');

        return view('${controller_name%Controller}.${method}');
    }"
                ;;
        esac

        # Aggiungi il metodo al contenuto del controller
        content+="${docblock}${method_signature}${method_body}"$'\n'
    done

    # Chiudi la classe
    content+="}"

    # Scrivi il contenuto nel file
    echo "$content" > "app/Http/Controllers/${controller_name}.php"

    echo "✅ Controller ${controller_name} creato con successo."
done

echo "🎉 Tutti i controller sono stati creati!"
