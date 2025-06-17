export async function displayError(message) {
    console.error(`🚀 displayError chiamato con messaggio: ${message}`);
    Swal.fire({
        icon: 'error',
        title: 'Errore!',
        text: message
    });
}

export async function displaySuccess(message) {
    console.error(`🚀 displayError chiamato con messaggio: ${message}`);
    Swal.fire({
        icon: 'success',
        title: 'Successo!',
        text: message
    });
}
export async function displayWarning(message) {
    console.error(`🚀 displayError chiamato con messaggio: ${message}`);
    Swal.fire({
        icon: 'warning',
        title: 'Attenzione!',
        text: message
    });
}
