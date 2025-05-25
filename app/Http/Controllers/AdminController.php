<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Facades\UltraError;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


/**
 * @Oracode Controller: AdminController
 * 🎯 Purpose: Handle Admin related operations
 * 🧱 Core Logic: Manages views and actions for Admin section
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-21
 */
class AdminController extends Controller
{
    /**
     * Logger instance
     *
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Display roles and permissions management page
     *
     * @return View
     */
public function rolesIndex(): View
    {
        $this->logger->info('Accessing roles and permissions management');

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.roles.index', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    /**
     * Display the form to assign roles to users
     *
     * @return View
     */
public function assignRoleForm(): View
    {
        $this->logger->info('Accessing assign role form');

        $users = \App\Models\User::all();
        $roles = Role::all();

        return view('admin.assign.role', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    /**
     * Assign roles to a user
     *
     * @param Request $request
     * @return RedirectResponse
     */
public function assignRole(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $user = \App\Models\User::findOrFail($validated['user_id']);
            $user->syncRoles($validated['roles']);

            $this->logger->info('Roles assigned to user', [
                'user_id' => $user->id,
                'roles' => $validated['roles']
            ]);

            return redirect()->route('admin.assign.role.form')
                ->with('success', __('admin.roles_assigned'));

        } catch (\Exception $e) {
            return UltraError::handle('ROLE_ASSIGNMENT_FAILED', [
                'error' => $e->getMessage()
            ], $e)->with('error', __('admin.role_assignment_failed'));
        }
    }

    /**
     * Display the form to assign permissions to roles
     *
     * @return View
     */
public function assignPermissionsForm(): View
    {
        $this->logger->info('Accessing assign permissions form');

        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.assign.permissions', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    /**
     * Assign permissions to a role
     *
     * @param Request $request
     * @return RedirectResponse
     */
public function assignPermissions(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $role = Role::findOrFail($validated['role_id']);
            $role->syncPermissions($validated['permissions']);

            $this->logger->info('Permissions assigned to role', [
                'role_id' => $role->id,
                'permissions' => $validated['permissions']
            ]);

            return redirect()->route('admin.assign.permissions.form')
                ->with('success', __('admin.permissions_assigned'));

        } catch (\Exception $e) {
            return UltraError::handle('PERMISSION_ASSIGNMENT_FAILED', [
                'error' => $e->getMessage()
            ], $e)->with('error', __('admin.permission_assignment_failed'));
        }
    }
}
