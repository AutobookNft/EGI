<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Facades\UltraError;

/**
 * @Oracode Controller: PersonalDataController
 * 🎯 Purpose: Handle PersonalData related operations
 * 🧱 Core Logic: Manages views and actions for PersonalData section
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-21
 */
class PersonalDataController_old extends Controller
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
     * Display account data management page
     *
     * @return View
     */
public function account(): View
    {
        $this->logger->info('Accessing account data management');

        return view('personal-data.account', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Display bio/profile management page
     *
     * @return View
     */
public function bio(): View
    {
        $this->logger->info('Accessing bio/profile management');

        return view('personal-data.bio', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Update user account data
     *
     * @param Request $request
     * @return RedirectResponse
     */
public function updateAccount(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
                // Altri campi...
            ]);

            $user = auth()->user();
            $user->update($validated);

            $this->logger->info('Account data updated', [
                'user_id' => $user->id
            ]);

            return redirect()->route('personal-data.account')
                ->with('success', __('personal_data.account_updated'));

        } catch (\Exception $e) {
            return UltraError::handle('ACCOUNT_UPDATE_FAILED', [
                'error' => $e->getMessage()
            ], $e)->with('error', __('personal_data.update_failed'));
        }
    }

    /**
     * Update user bio/profile data
     *
     * @param Request $request
     * @return RedirectResponse
     */
public function updateBio(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'bio' => 'nullable|string|max:1000',
                'social_links' => 'nullable|array',
                // Altri campi...
            ]);

            $user = auth()->user();
            $user->profile()->update($validated);

            $this->logger->info('Bio/profile updated', [
                'user_id' => $user->id
            ]);

            return redirect()->route('personal-data.bio')
                ->with('success', __('personal_data.bio_updated'));

        } catch (\Exception $e) {
            return UltraError::handle('BIO_UPDATE_FAILED', [
                'error' => $e->getMessage()
            ], $e)->with('error', __('personal_data.update_failed'));
        }
    }
}
