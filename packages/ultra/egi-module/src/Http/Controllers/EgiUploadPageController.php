<?php

namespace Ultra\EgiModule\Http\Controllers;

use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use App\Models\User;
use Throwable;

/**
 * 📜 Oracode Controller: EgiUploadPageController
 * Manages HTTP requests related to displaying the EGI upload page interface.
 *
 * @package     Ultra\EgiModule\Http\Controllers
 * @author      Fabio Cherici <fabiocherici@gmail.com>
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     1.3.0 // Improved UEM integration and error handling
 * @since       2025-04-19
 *
 * @purpose     🎯 Handles GET requests to display the primary user interface used by the administrator
 *              in Q1 to upload EGI files on behalf of Creators. Prepares minimal necessary context data for the view.
 *
 * @context     🧩 Invoked by the web routing layer ('egi.upload.page'). Operates within the standard Laravel web
 *              middleware group, requiring administrator authentication.
 *
 * @state       💾 Stateless. Uses injected dependencies (AuthFactory, UltraLogManager) instead of facades.
 *
 * @feature     🗝️ Renders the core Blade view (`egimodule::uploading_files`).
 * @feature     🗝️ Passes the authenticated administrator's User object to the view.
 * @feature     🗝️ Integrates UltraErrorManager for error handling and logging via DI.
 * @feature     🗝️ Implements configurable error codes tailored to specific EGI UI scenarios.
 * @feature     🗝️ Uses custom error definitions for notices and standardized error handling.
 *
 * @signal      🚦 Returns `Illuminate\Contracts\View\View` response on success.
 * @signal      🚦 Returns `Illuminate\Http\RedirectResponse` for unauthenticated access or errors.
 * @signal      🚦 Can throw framework exceptions, handled by UEM.
 *
 * @privacy     🛡️ Accesses authenticated administrator's user data via injected AuthFactory.
 * @privacy     🛡️ `@privacy-internal`: Reads administrator's `User` model instance.
 * @privacy     🛡️ `@privacy-lawfulBasis`: Processing necessary for the performance of the administrator's task.
 * @privacy     🛡️ `@privacy-purpose`: User data used for authentication and minimal display in the view.
 * @privacy     🛡️ `@privacy-data`: Passes only the administrator's `User` object to the view.
 * @privacy     🛡️ `@privacy-consideration`: UEM sanitizes context data to prevent PII exposure.
 *
 * @dependency  🤝 Laravel Framework (Request, Controller, View, RedirectResponse).
 * @dependency  🤝 UltraErrorManager (ErrorManagerInterface).
 * @dependency  🤝 UltraLogManager (UltraLogManager).
 * @dependency  🤝 Auth (AuthFactory).
 * @dependency  🤝 Models: `App\Models\User`.
 * @dependency  🤝 View: `egimodule::uploading_files`.
 * @dependency  🤝 Route: Definition in `routes/web.php` named 'egi.upload.page'.
 *
 * @testing     🧪 Feature Test: Simulate authenticated GET request, assert 200 and view data.
 * @testing     🧪 Feature Test: Simulate unauthenticated GET request, assert redirect to /login.
 * @testing     🧪 Unit Test: Verify UEM error handling for authentication and unexpected errors.
 *
 * @rationale   💡 Provides a simple controller for rendering the EGI upload interface, with robust error handling via UEM.
 *
 * @changelog   1.3.0 - 2025-04-29: Improved UEM integration with proper error code definitions.
 *                                   Removed facades in favor of dependency injection.
 *                                   Added structured approach to error handling.
 *                                   Implemented UltraLogManager for standardized logging.
 */
class EgiUploadPageController extends Controller
{
    /**
     * 🧱 @dependency ErrorManagerInterface instance.
     * Used for standardized error handling.
     * @var ErrorManagerInterface
     */
    protected readonly ErrorManagerInterface $errorManager;

    /**
     * 🧱 @dependency UltraLogManager instance.
     * Used for standardized logging.
     * @var UltraLogManager
     */
    protected readonly UltraLogManager $logger;

    /**
     * 🧱 @dependency AuthFactory instance.
     * Used for user authentication status.
     * @var AuthFactory
     */
    protected readonly AuthFactory $auth;

    /**
     * 🎯 Constructor: Injects required dependencies.
     *
     * @param ErrorManagerInterface $errorManager Ultra Error Manager interface for standardized error handling
     * @param UltraLogManager $logger Ultra Log Manager for standardized logging
     * @param AuthFactory $auth Laravel Auth Factory for user authentication
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        AuthFactory $auth
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->auth = $auth;

        // Define custom error codes for EGI UI scenarios
        $this->defineEgiUiErrorCodes();
    }

    /**
     * 🚀 Display the main page for uploading EGI files (Administrator Q1 Workflow).
     * @purpose Show the Blade view interface for the administrator to upload EGI files.
     * @usage Mapped via route: GET /upload/egi (named 'egi.upload.page')
     *
     * --- Logic ---
     * 1. Define the target Blade view name.
     * 2. Retrieve the authenticated administrator user via AuthFactory.
     * 3. If unauthenticated, use UEM to handle 'EGI_UNAUTHORIZED_ACCESS' and redirect to login.
     * 4. Log successful access via ULM.
     * 5. Prepare context data with the user object.
     * 6. Return the rendered Blade view, handling any exceptions with UEM.
     * --- End Logic ---
     *
     * @param Request $request The incoming HTTP request object.
     * @return View|RedirectResponse The rendered Blade view or redirect response for errors.
     *
     * @privacy-purpose To retrieve administrator context for rendering the upload page.
     * @privacy-data Retrieves administrator's User object. Passes it to the view.
     * @privacy-lawfulBasis Necessary for providing the requested service interface.
     */
    public function showUploadPage(Request $request): View|RedirectResponse
    {
        $viewName = 'egimodule::uploading_files';

        // Prepare context with metadata for logging/error handling
        $context = [
            'controller' => static::class,
            'method' => __FUNCTION__,
            'request_path' => $request->path(),
            'request_ip' => $request->ip()
        ];

        try {
            /** @var User|null $adminUser Retrieve authenticated user via injected AuthFactory */
            $adminUser = $this->auth->guard()->user();

            // Check if user is authenticated
            if (!$adminUser) {
                // Record authentication failure and return redirect response
                $this->logger->warning('Unauthenticated access attempt to EGI upload page', $context);

                return $this->errorManager->handle(
                    'EGI_UNAUTHORIZED_ACCESS',
                    $context
                ) ?? redirect()->to('/login')->with('error', 'Authentication required');
            }

            // Enhance context with authenticated user information
            $context['user_id'] = $adminUser->id;
            $context['user_email'] = $adminUser->email;

            // Log successful page access via UltraLogManager
            $this->logger->info('EGI upload page accessed successfully', $context);

            // Collezioni in evidenza (3 items)
            $featured = Collection::where('is_featured', true)
                ->take(3)
                ->get();

            // Ultime gallerie (8 items)
            $recent = Collection::orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            // Prepare data for the Blade view
            $data = [
                'user' => $adminUser,
                'featured' => $featured,
                'recent' => $recent,
                // Add other necessary view data here
            ];

            // Render and return the view
            return view($viewName, $data);

        } catch (Throwable $e) {
            // Log the exception via UltraLogManager
            $this->logger->error('Exception during EGI upload page rendering', array_merge($context, [
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage()
            ]));

            // Handle the error via UEM, defaulting to a redirect if UEM doesn't provide a response
            return $this->errorManager->handle(
                'EGI_PAGE_RENDERING_ERROR',
                $context,
                $e
            ) ?? redirect()->to('/home')->with('error', 'Error loading the upload page');
        }
    }

    /**
     * 🧱 Define custom error codes specific to EGI UI operations.
     * Registers these codes with the UEM for consistent error handling.
     *
     * @return void
     */
    protected function defineEgiUiErrorCodes(): void
    {
        // Define notice for successful page access (log-only)
        $this->errorManager->defineError('EGI_PAGE_ACCESS_NOTICE', [
            'type' => 'notice',
            'blocking' => 'not',
            'dev_message' => 'EGI upload page accessed successfully',
            'user_message' => null, // No user-visible message needed
            'http_status_code' => 200,
            'msg_to' => 'log-only', // Only log, don't display
        ]);

        // Define error for unauthorized access
        $this->errorManager->defineError('EGI_UNAUTHORIZED_ACCESS', [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message' => 'Unauthenticated access attempt to EGI upload page',
            'user_message' => 'Authentication required to access this page',
            'http_status_code' => 401,
            'msg_to' => 'sweet-alert', // Use SweetAlert for user notification
            'devTeam_email_need' => false, // Don't need email for standard auth failures
        ]);

        // Define error for page rendering failure
        $this->errorManager->defineError('EGI_PAGE_RENDERING_ERROR', [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message' => 'Exception during EGI upload page rendering',
            'user_message' => 'Unable to load the upload page. Please try again later.',
            'http_status_code' => 500,
            'msg_to' => 'sweet-alert',
            'devTeam_email_need' => true, // This is unexpected, notify team
        ]);
    }
}
