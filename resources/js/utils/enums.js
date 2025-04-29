let enumPromise = null;

export function loadEnums() {
    if (!enumPromise) {
        enumPromise = new Promise(async (resolve, reject) => {
            try {
                console.log("⏳ Caricamento ENUM...");

                const response = await fetch('/js/enums', {
                    headers: {
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                  })

                console.log('ENUM response', response);
                if (!response.ok) throw new Error(`Errore nel caricamento ENUM: ${response.status}`);

                const data = await response.json();
                window.enums = data;
                console.log("✅ ENUM caricati:", window.enums);
                resolve(data);
            } catch (error) {
                console.error("❌ Errore nel caricamento degli ENUM:", error);
                window.enums = {}; // Preveniamo crash con un oggetto vuoto
                reject(error);
            }
        });
    }
    return enumPromise;
}

// ✅ Ora `getEnum()` aspetta il caricamento degli ENUM prima di eseguire il codice
export async function getEnum(enumGroup, key) {
    await loadEnums();
    return window.enums?.[enumGroup]?.[key] || null;
}

// ✅ `isPendingStatus()` ora aspetta gli ENUM prima di eseguire il controllo
export async function isPendingStatus(status) {
    await loadEnums(); // ⏳ Aspettiamo che gli ENUM siano caricati prima di eseguire la funzione

    const pendingStatuses = [
        await getEnum("NotificationStatus", "PENDING"),
        await getEnum("NotificationStatus", "PENDING_CREATE"),
        await getEnum("NotificationStatus", "PENDING_UPDATE")
    ].filter(Boolean); // Rimuove eventuali valori null

    console.log("🔍 ENUM trovati per pending:", pendingStatuses);
    return pendingStatuses.includes(status);
}

