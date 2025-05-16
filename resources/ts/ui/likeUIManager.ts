/**
 * @Oracode Like UI Manager
 * 🎯 Purpose: Manages UI updates for like operations
 * 🧱 Core Logic: DOM manipulation, state synchronization
 *
 * @package FlorenceEGI/UI
 * @author Padmin D. Curtis
 * @version 1.0.0
 * @date 2025-05-15
 */

import likeService, { LikeableResource, LikeResponse } from '../services/likeService';
import { AppConfig, appTranslate } from '../config/appConfig';
import { UEM_Client_TS_Placeholder as UEM } from '../services/uemClientService';
import { getAuthStatus } from '../features/auth/authService';

interface LikeButton extends HTMLButtonElement {
    dataset: {
        resourceType: string;
        resourceId: string;
        likeUrl?: string;
    };
}

export class LikeUIManager {
    private static instance: LikeUIManager;
    private initialized: boolean = false;
    private processingButtons: Set<string> = new Set();
    private config: AppConfig | null = null;

    private constructor() {}

    public static getInstance(): LikeUIManager {
        if (!LikeUIManager.instance) {
            LikeUIManager.instance = new LikeUIManager();
        }
        return LikeUIManager.instance;
    }

    /**
     * Initialize like functionality
     */
    public initialize(config: AppConfig): void {
        if (this.initialized) {
            console.log('[LikeUIManager] Already initialized');
            return;
        }

        this.config = config;
        console.log('[LikeUIManager] Initializing...');

        // Use event delegation for all like buttons
        document.addEventListener('click', this.handleDocumentClick.bind(this));

        // Listen for collection changes to refresh UI
        document.addEventListener('collection-changed', this.handleCollectionChanged.bind(this));

        this.initialized = true;
        console.log('[LikeUIManager] Initialized successfully');
    }

    /**
     * Handle document click with event delegation
     */
    private handleDocumentClick(event: Event): void {
        const target = event.target as HTMLElement;
        const likeButton = target.closest('.like-button') as LikeButton | null;

        console.log('[LikeUIManager] Document clicked:', target);

        if (likeButton) {
            event.preventDefault();
            event.stopPropagation();
            this.handleLikeClick(likeButton);
        }
    }

    /**
     * Handle collection changed event
     */
    private handleCollectionChanged(event: Event): void {
        // Refresh like counts when collection changes
        document.querySelectorAll('.like-button').forEach(button => {
            const likeButton = button as LikeButton;
            const resource: LikeableResource = {
                type: likeButton.dataset.resourceType as 'collection' | 'egi',
                id: parseInt(likeButton.dataset.resourceId, 10)
            };

            // Qui potresti fare una chiamata API per aggiornare il conteggio
            // Per ora lasciamo vuoto finché non implementiamo l'endpoint GET dei like
        });
    }

    /**
     * Handle like button click
     */
    private async handleLikeClick(button: LikeButton): Promise<void> {

        console.log('[LikeUIManager] Like button clicked:', button);

        if (!this.config) {
            console.error('[LikeUIManager] Config not initialized');
            return;
        }

        // Verifica che l'utente sia almeno "connected"
        const authStatus = getAuthStatus(this.config);
        if (authStatus === 'disconnected') {
            // Mostra messaggio o apri modal wallet connect
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'info',
                    title: appTranslate('authRequiredTitle', this.config.translations),
                    text: appTranslate('authRequiredForLike', this.config.translations),
                    confirmButtonText: appTranslate('connectWallet', this.config.translations),
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Trigger apertura modale wallet
                        document.dispatchEvent(new CustomEvent('open-wallet-modal'));
                    }
                });
            }
            return;
        }

        const resource: LikeableResource = {
            type: button.dataset.resourceType as 'collection' | 'egi',
            id: parseInt(button.dataset.resourceId, 10)
        };

        const key = `${resource.type}-${resource.id}`;

        // Prevent double-clicks
        if (this.processingButtons.has(key)) {
            console.log(`[LikeUIManager] Already processing ${key}`);
            return;
        }

        this.processingButtons.add(key);
        button.disabled = true;

        // Add loading state
        button.classList.add('loading');

        try {
            const response = await likeService.toggleLike(resource, this.config);
            this.updateUI(button, response);
            this.updateRelatedElements(resource, response);

        } catch (error) {
            // Error già gestito da service con UEM
            console.error('[LikeUIManager] Failed to toggle like:', error);
        } finally {
            button.disabled = false;
            button.classList.remove('loading');
            this.processingButtons.delete(key);
        }
    }

    /**
     * Update button UI after like toggle
     */
    private updateUI(button: LikeButton, response: LikeResponse): void {
        const { is_liked, likes_count } = response;

        // Toggle button state
        button.classList.toggle('is-liked', is_liked);

        // Update heart icon
        const icon = button.querySelector('.icon-heart');
        if (icon) {
            icon.classList.toggle('text-pink-500', is_liked);
            icon.classList.toggle('text-gray-400', !is_liked);
        }

        // Update text usando appTranslate dal tuo sistema
        const text = button.querySelector('.like-text');
        if (text && this.config) {
            const likedText = appTranslate('liked', this.config.translations);
            const likeText = appTranslate('like', this.config.translations);
            text.textContent = is_liked ? likedText : likeText;
        }

        // Update counter
        const counter = button.querySelector('.like-count-display');
        if (counter) {
            counter.textContent = `(${likes_count})`;
        }
    }

    /**
     * Update all related elements on the page
     */
    private updateRelatedElements(resource: LikeableResource, response: LikeResponse): void {
        const { is_liked, likes_count } = response;

        // Find all elements showing this resource's like count
        const selector = `[data-resource-type="${resource.type}"][data-resource-id="${resource.id}"]`;
        const relatedElements = document.querySelectorAll(selector);

        relatedElements.forEach(element => {
            if (element.classList.contains('like-count-display') && element !== document.activeElement) {
                element.textContent = likes_count.toString();
            }
        });
    }
}

export default LikeUIManager.getInstance();
