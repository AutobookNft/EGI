// Debug script per testare il dropdown delle collezioni
console.log('=== DEBUG COLLECTION DROPDOWN ===');

// Controlla se l'elemento esiste
const dropdown = document.getElementById('collection-list-dropdown-button');
console.log('Dropdown button element:', dropdown);

if (dropdown) {
    console.log('Dropdown button classes:', dropdown.className);
    console.log('Dropdown button visible:', dropdown.offsetWidth > 0 && dropdown.offsetHeight > 0);

    // Testa se ha event listeners
    console.log('Dropdown button listeners (esistenti):', dropdown.onclick);

    // Aggiungi un listener di test
    dropdown.addEventListener('click', function(e) {
        console.log('CLICK INTERCETTATO!', e);
        e.preventDefault();
        e.stopPropagation();

        // Controlla il menu
        const menu = document.getElementById('collection-list-dropdown-menu');
        console.log('Menu element:', menu);

        if (menu) {
            console.log('Menu classes:', menu.className);
            console.log('Menu is hidden:', menu.classList.contains('hidden'));

            // Test toggle
            menu.classList.toggle('hidden');
            console.log('Toggled menu visibility');
        }
    });

    console.log('Test listener aggiunto al dropdown');
} else {
    console.log('ERRORE: Dropdown button non trovato!');
}

// Controlla se l'utente ha i permessi
console.log('Dropdown container parent:', document.getElementById('collection-list-dropdown-container'));

// Test API call
fetch('/api/user/accessible-collections', {
    method: 'GET',
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
})
.then(response => {
    console.log('API Response status:', response.status);
    return response.text();
})
.then(data => {
    console.log('API Response data:', data);
})
.catch(error => {
    console.log('API Error:', error);
});
