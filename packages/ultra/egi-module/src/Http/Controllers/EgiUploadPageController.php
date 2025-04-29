<?php

namespace  Ultra\EgiModule\Http\Controllers;

use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\User; // Assuming User model namespace
use App\Models\Team; // Assuming Team model namespace (adjust if different)
use Throwable; // Import Throwable for potential exceptions


/**
 * 📜 Oracode Controller: EgiUploadPageController
 * Manages HTTP requests related to displaying the EGI upload page interface.
 * (DocBlock header remains similar to the previous corrected version - focusing on PURPOSE)
 *
 * @package     App\Http\Controllers
 * @author      Fabio Cherici <fabiocherici@gmail.com>
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     1.1.0 // Logic corrected for Q1 Admin Upload Scenario
 * @since       2025-04-19
 *
 * @purpose     🎯 Handles GET requests to display the primary user interface used by the administrator (Fabio)
 *              in Q1 to upload EGI files on behalf of Creators. Prepares minimal necessary context data for the view.
 *
 * @context     🧩 Invoked by the web routing layer ('egi.upload.page'). Operates within the standard Laravel web
 *              middleware group, requiring administrator authentication.
 *
 * @state       💾 Stateless. Reads administrator's authentication state (Auth facade).
 *
 * @feature     🗝️ Renders the core Blade view (`vendor.uploadmanager.uploading_files`).
 * @feature     🗝️ Passes the authenticated administrator's User object to the view (for potential UI display/logging).
 * @feature     🗝️ Uses standard Laravel Facades (Auth, Log, View).
 * @feature     🗝️ Decoupled from specific Collection/Team context at page load time (handled during POST).
 *
 * @signal      🚦 Returns `Illuminate\Contracts\View\View` response on success.
 * @signal      🚦 Logs administrator page access via standard `Log` facade.
 * @signal      🚦 Can implicitly throw framework exceptions.
 *
 * @privacy     🛡️ Accesses authenticated administrator's user data (`Auth::user()`).
 * @privacy     🛡️ `@privacy-internal`: Reads administrator's `User` model instance.
 * @privacy     🛡️ `@privacy-lawfulBasis`: Processing necessary for the performance of the administrator's task (uploading EGIs).
 * @privacy     🛡️ `@privacy-purpose`: User data used solely for authentication confirmation and potentially minimal display in the view header/footer.
 * @privacy     🛡️ `@privacy-data`: Passes only the administrator's `User` object to the view. Target Collection/Creator context is NOT passed here.
 * @privacy     🛡️ `@privacy-consideration`: Ensure the Blade view handles the absence of specific Collection data gracefully at load time and relies on user input or JS logic to determine the target during the actual upload POST.
 *
 * @dependency  🤝 Laravel Framework (Request, Auth, Log, View, Controller).
 * @dependency  🤝 Models: `App\Models\User`.
 * @dependency  🤝 View: `resources/views/vendor/uploadmanager/uploading_files.blade.php`.
 * @dependency  🤝 Route: Definition in `routes/web.php` named 'egi.upload.page'.
 *
 * @testing     🧪 Feature Test: Simulate authenticated GET request as administrator. Assert 200, correct view, presence of admin user data.
 * @testing     🧪 Feature Test: Simulate unauthenticated GET request. Assert redirect.
 *
 * @rationale   💡 Provides a simple controller focused solely on rendering the upload interface for the Q1 administrator workflow.
 *              Avoids unnecessary coupling with Collection/Team context at this stage, aligning with the Q1 backend management plan.
 */
class EgiUploadPageController extends Controller
{
    /**
     * Log channel for this handler.
     * @var string
     */
    protected string $logChannel = 'upload'; // Default channel

    /**
     * 🚀 Display the main page for uploading EGI files (Administrator Q1 Workflow).
     * @purpose Show the Blade view interface for the administrator to upload EGI files.
     * @usage Mapped via route: GET /upload/egi (named 'egi.upload.page')
     *
     * --- Logic ---
     * 1. Define the target Blade view name.
     * 2. Retrieve the authenticated administrator user via `Auth::user()`.
     * 3. Prepare context array (`$data`) containing only the administrator's user object.
     * 4. Log the administrator page access event.
     * 5. Return the rendered Blade view, passing the minimal context data.
     * --- End Logic ---
     *
     * @param Request $request The incoming HTTP request object.
     * @return View The rendered Blade view (`vendor.uploadmanager.uploading_files`).
     *
     * @throws Throwable Can potentially throw framework exceptions.
     *
     * @privacy-purpose To retrieve administrator context for rendering the upload page.
     * @privacy-data Retrieves administrator's User object. Passes it to the view.
     * @privacy-lawfulBasis Necessary for providing the requested service interface to the authenticated administrator.
     */
    public function showUploadPage(Request $request): View
    {
        $viewName = 'egimodule::uploading_files';
        $logContext = ['controller' => static::class, 'method' => __FUNCTION__];

        /** @var User|null $adminUser Retrieve authenticated user (expected to be admin in Q1) */
        $adminUser = Auth::user();

        // Basic check if user is authenticated
        if (!$adminUser) {
            Log::channel($this->logChannel)->warning('[EgiUploadPageController] Unauthenticated attempt to access EGI upload page.', $logContext);
            // Middleware should handle redirect, but we can abort defensively
            abort(403, 'Unauthorized access.');
        }

        $logContext['user_id'] = $adminUser->id;
        Log::channel($this->logChannel)->info('[EgiUploadPageController] Showing EGI upload page for administrator.', $logContext);

        // Prepare minimal data for the Blade view
        $data = [
            'user' => $adminUser, // Pass the admin user object
            // NO Team/Collection context passed here for Q1 admin workflow
        ];

        // Render and return the view
        return view($viewName, $data);
    }
}
