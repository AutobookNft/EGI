<?php

/**
 * @Oracode Display Single Biography Page
 * 🎯 Purpose: Show biography with chapters and hybrid authentication awareness
 * 🧱 Core Logic: Access control for private biographies, chapter filtering by publication status
 * 🛡️ Security: Owner verification, permission checks, comprehensive audit logging
 *
 * @param Request $request HTTP request
 * @param Biography $biography Biography instance from route model binding
 * @return View Biography detail page
 *
 * @throws \Ultra\ErrorManager\Exceptions\UltraErrorException When access denied or display fails
 */
public function show(Request $request, Biography $biography): View
{
    $authType = FegiAuth::getAuthType();
    $userId = FegiAuth::id();
    $walletAddress = FegiAuth::getWallet();

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
        $user = FegiAuth::user();

        // Access control validation
        if (!$biography->is_public && (!FegiAuth::check() || $biography->user_id !== $userId)) {
            // Log security event for unauthorized access attempt
            $this->auditService->logSecurityEvent(
                $user ?? new \stdClass(),
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

        // ========== CARICAMENTO CORRETTO MEDIA SPATIE ==========

        // Load user relationship
        $biography->load(['user:id,name,email,created_at']);

        // Preload ALL media collections using Spatie methods
        $biography->loadMissing('media'); // Ensure basic media relationship is loaded

        // Load chapters based on access level with their media
        if ($isOwner) {
            // Owner: all chapters with media preloaded
            $chapters = $biography->chapters()
                ->with(['media']) // Preload media for chapters
                ->timelineOrdered()
                ->get();
        } else {
            // Public access: published chapters only with media preloaded
            $chapters = $biography->publishedChapters()
                ->with(['media']) // Preload media for chapters
                ->timelineOrdered()
                ->get();
        }

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
                $user,
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
            'biography_media_count' => $biography->getMedia()->count(),
            'main_gallery_count' => $biography->getMedia('main_gallery')->count(),
            'featured_image_exists' => $biography->getFirstMedia('featured_image') ? true : false
        ]);

        return view('biography.show', [
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

        // This code is never reached - UEM throws UltraErrorException for blocking errors
    }
}
