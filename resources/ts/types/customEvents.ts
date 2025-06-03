// File: resources/ts/types/customEvents.ts
/**
 * 📜 Oracode TypeScript Module: Custom Events Type Declarations
 * 🎯 Purpose: Extend DocumentEventMap for FEGI custom events
 * 🔧 TypeScript: Proper type safety for custom events
 *
 * @version 1.0.0
 * @date 2025-05-29
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// Extend the global DocumentEventMap interface
declare global {
    interface DocumentEventMap {
        'openUploadModal': CustomEvent<{
            type: 'egi' | 'collection' | string;
        }>;
        'fegiConnectionComplete': CustomEvent<{
            walletAddress?: string;
            userStatus?: string;
        }>;
        'fegiAuthenticationSuccess': CustomEvent<{
            walletAddress: string;
            userName: string;
            userStatus: string;
        }>;
        'fegiAccountCreated': CustomEvent<{
            walletAddress: string;
            fegiKey: string;
            userName: string;
        }>;
    }
}

// Export empty object to make this a module
export {};
