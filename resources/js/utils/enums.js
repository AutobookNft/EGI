export async function loadEnums() {
    try {
        const response = await fetch('/js/enums');
        if (!response.ok) {
            throw new Error(`Errore nel caricamento degli enum: ${response.status}`);
        }

        const data = await response.json();
        window.enums = data;
        console.log("✅ Enum caricati:", window.enums);
    } catch (error) {
        console.error("❌ Errore nel caricamento degli enum:", error);
        window.enums = {}; // In caso di errore, evita crash lasciando un oggetto vuoto
    }
}

// Funzione helper per ottenere un valore di enum
export function getEnum(enumGroup, key) {
    return window.enums?.[enumGroup]?.[key] || null;
}
