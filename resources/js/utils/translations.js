export async function fetchTranslations() {
    try {
        console.log("🔄 Tentativo di caricare le traduzioni...");

        const response = await fetch('/translations.js');
        if (!response.ok) throw new Error(`Errore nel caricamento delle traduzioni: ${response.status}`);

        const scriptText = await response.text();
        eval(scriptText); // Carica window.translations

        if (typeof window.translations === "undefined") {
            throw new Error("❌ window.translations non è stato definito correttamente!");
        }

        console.log("✅ Traduzioni caricate correttamente:", window.translations);
    } catch (error) {
        console.error("❌ Errore nel caricamento delle traduzioni:", error);
        window.translations = {};
    }
}

export async function ensureTranslationsLoaded() {
    if (!window.translations || Object.keys(window.translations).length === 0) {
        await fetchTranslations();
    }
}

// Funzione globale per ottenere una traduzione
export function getTranslation(key, replacements = {}) {
    let keys = key.split('.');
    let translation = window.translations;

    for (let i = 0; i < keys.length; i++) {
        translation = translation?.[keys[i]];
        if (typeof translation === "undefined") {
            console.warn("⚠️ Traduzione non trovata per la chiave:", key);
            return key; // Restituisce la chiave originale se non trovata
        }
    }

    for (const placeholder in replacements) {
        translation = translation.replace(`:${placeholder}`, replacements[placeholder]);
    }

    return translation;
}
