// Global type definitions
declare global {
    interface Window {
        // ... esistenti ...
        likeManager?: typeof import('./ui/likeManager').default;
    }
}
