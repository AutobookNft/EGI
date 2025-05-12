export async function fetchTranslations() {
    try {
        console.log("🔄 Caricamento traduzioni...");

        const response = await fetch('/translations.json');
        if (!response.ok) throw new Error(`Errore nel caricamento delle traduzioni: ${response.status}`);

        const data = await response.json();
        window.translations = data;

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

export function getTranslation(key, replacements = {}) {
    let keys = key.split('.');
    let translation = window.translations;

    for (let i = 0; i < keys.length; i++) {
        translation = translation?.[keys[i]];
        if (typeof translation === "undefined") {
            console.warn("⚠️ Traduzione non trovata per la chiave:", key);
            return key;
        }
    }

    for (const placeholder in replacements) {
        translation = translation.replace(`:${placeholder}`, replacements[placeholder]);
    }

    return translation;
}
