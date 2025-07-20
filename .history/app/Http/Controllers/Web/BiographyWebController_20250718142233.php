public function show(Request $request, $creator_id): View | RedirectResponse | Response
{
    $authType = FegiAuth::getAuthType();
    $userId = FegiAuth::id();
    $walletAddress = FegiAuth::getWallet();

    $biography = Biography::where('user_id', $creator_id)->first();

    // Check if biography exists
    if (!$biography) {
        abort(404, 'Biografia non trovata');
    }

    $this->logger->info('Biography page requested', [
        'user_id' => $userId,
        'auth_type' => $authType,
        'biography_id' => $biography->id,
        'biography_slug' => $biography->slug,
        'biography_title' => $biography->title,
        'is_public' => $biography->is_public,
        'biography_type' => $biography->type,
        'owner_id' => $biography->user_id,
        'wallet' => $walletAddress,
        'ip_address' => $request->ip()
    ]);

    try {
        $currentUser = FegiAuth::user(); // Utente attualmente autenticato
        $biographyOwner = User::findOrFail($creator_id); // Proprietario della biografia

        // Access control validation
        if (!$biography->is_public && (!FegiAuth::check() || $biography->user_id !== $userId)) {
            // Log security event for unauthorized access attempt
            $this->auditService->logSecurityEvent(
                $currentUser ?? new \stdClass(),
                'unauthorized_biography_access',
                [
                    'biography_id' => $biography->id,
                    'biography_title' => $biography->title,
                    'owner_id' => $biography->user_id,
                    'attempted_by_user_id' => $userId,
                    'attempted_action' => 'view_private_biography',
                    'auth_type' => $authType,
                    'wallet_address' => $walletAddress,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                'medium'
            );

            $this->errorManager->handle('BIOGRAPHY_ACCESS_DENIED', [
                'user_id' => $userId,
                'auth_type' => $authType,
                'biography_id' => $biography->id,
                'owner_id' => $biography->user_id,
                'operation' => 'view',
                'is_public' => $biography->is_public
            ], new \Illuminate\Auth\Access\AuthorizationException());
        }

        // Determine ownership and access type
        $isOwner = FegiAuth::check() && $biography->user_id === $userId;
        $accessType = $isOwner ? 'owner' : 'public';

        // Load chapters based on access level
        if ($isOwner) {
            // Owner: all chapters with media
            $chapters = $biography->chapters()
                ->with(['media'])
                ->timelineOrdered()
                ->get();
        } else {
            // Public access: published chapters only with media
            $chapters = $biography->publishedChapters()
                ->with(['media'])
                ->timelineOrdered()
                ->get();
        }

        // ========== FIX MEDIA SPATIE ==========

        // Load user relationship correctly
        $biography->load(['user:id,name,email,created_at']);

        // Force reload media using Spatie methods
        $biography->refresh(); // Refresh the model from database

        // Alternative: Load media directly without using relationships
        $biographyMediaIds = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('model_type', Biography::class)
            ->where('model_id', $biography->id)
            ->pluck('id');

        // Calculate reading time
        $estimatedReadingTime = $biography->getEstimatedReadingTime();

        // Prepare navigation data for chapters
        $chapterNavigation = $chapters->map(function ($chapter, $index) {
            return [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'slug' => $chapter->slug,
                'date_range' => $chapter->dateRangeDisplay,
                'type' => $chapter->chapter_type,
                'position' => $index + 1
            ];
        });

        // GDPR audit logging (if authenticated)
        if (FegiAuth::check()) {
            $this->auditService->logUserAction(
                $currentUser,
                'biography_viewed',
                [
                    'entity_type' => 'Biography',
                    'entity_id' => $biography->id,
                    'biography_title' => $biography->title,
                    'biography_type' => $biography->type,
                    'access_type' => $accessType,
                    'auth_type' => $authType,
                    'chapters_viewed' => $chapters->count(),
                    'total_chapters' => $biography->chapters()->count(),
                    'estimated_reading_time' => $estimatedReadingTime,
                    'wallet_address' => $walletAddress,
                    'referrer' => $request->header('referer')
                ],
                GdprActivityCategory::DATA_ACCESS
            );
        }

        $this->logger->info('Biography page rendered successfully', [
            'user_id' => $userId,
            'auth_type' => $authType,
            'biography_id' => $biography->id,
            'chapters_count' => $chapters->count(),
            'access_type' => $accessType,
            'is_owner' => $isOwner,
            'reading_time' => $estimatedReadingTime,
            // DEBUG INFO
            'media_direct_count' => $biographyMediaIds->count(),
            'biography_refresh_attempted' => true
        ]);

        return view('biography.show', [
            'user' => $biographyOwner, // ← FIX: Proprietario della biografia, NON utente corrente
            'biography' => $biography,
            'chapters' => $chapters,
            'chapterNavigation' => $chapterNavigation,
            'isOwner' => $isOwner,
            'authType' => $authType,
            'accessType' => $accessType,
            'estimatedReadingTime' => $estimatedReadingTime,
            'canEditBiography' => $isOwner && FegiAuth::can('edit_biography'),
            'canCreateChapter' => $isOwner && FegiAuth::can('create_chapter'),
            'canManageChapters' => $isOwner && FegiAuth::can('manage_chapters'),
            'walletAddress' => $walletAddress,
            'isAuthenticated' => FegiAuth::check(),
            'title' => $biography->title,
            'metaDescription' => $biography->contentPreview,
            'canonicalUrl' => route('biography.public.show', $biography->slug)
        ]);
    } catch (\Exception $e) {
        $this->logger->error('Biography page rendering failed', [
            'user_id' => $userId,
            'auth_type' => $authType,
            'biography_id' => $biography->id,
            'error' => $e->getMessage(),
            'error_line' => $e->getLine(),
            'error_file' => $e->getFile(),
            'stack_trace' => $e->getTraceAsString()
        ]);

        // UEM handles error display based on configuration (msg_to: sweet-alert, toast, etc.)
        $this->errorManager->handle('BIOGRAPHY_SHOW_FAILED', [
            'user_id' => $userId,
            'auth_type' => $authType,
            'biography_id' => $biography->id,
            'biography_slug' => $biography->slug,
            'is_public' => $biography->is_public
        ], $e);

        // Fallback in caso di eccezione
        return response('Errore interno', 500);
    }
}
