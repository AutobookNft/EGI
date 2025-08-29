// Script di debug per testare l'API delle collezioni
// Esegui questo nella console del browser quando sei loggato

async function testCollectionAPI() {
    console.log('🧪 Testing Collection API...');

    try {
        const response = await fetch('/api/user/accessible-collections', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        });

        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', response.headers);

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'PARSE_ERROR' }));
            console.error('❌ API Error:', errorData);
            return null;
        }

        const data = await response.json();
        console.log('✅ API Success:', data);
        console.log('📊 Owned collections:', data.owned_collections?.length || 0);
        console.log('📊 Collaborating collections:', data.collaborating_collections?.length || 0);

        return data;
    } catch (error) {
        console.error('💥 Network Error:', error);
        return null;
    }
}

// Testa anche il click del dropdown
function testDropdownClick() {
    console.log('🖱️ Testing dropdown click...');
    const button = document.getElementById('collection-list-dropdown-button');
    if (button) {
        console.log('✅ Dropdown button found');
        button.click();
    } else {
        console.error('❌ Dropdown button not found');
    }
}

console.log('🔧 Debug functions loaded:');
console.log('   - testCollectionAPI()');
console.log('   - testDropdownClick()');
