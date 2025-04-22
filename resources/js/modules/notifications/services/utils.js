export async function displayError(message) {
    console.error(`🚀 displayError chiamato con messaggio: ${message}`);
    Swal.fire({
        icon: 'error',
        title: 'Errore!',
        text: message
    });
}
