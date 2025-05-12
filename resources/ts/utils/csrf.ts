// csrf.ts
// File: resources/ts/utils/csrf.ts
export function getCsrfTokenTS(): string {
    const tokenMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    return tokenMeta?.content || '';
}