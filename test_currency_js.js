// Test currency conversion directly in browser console
console.log('🧪 Testing currency conversion...');

// Simula il cambio valuta come se l'utente avesse cliccato
const testCurrency = async () => {
    try {
        // Trigger currency change event
        const event = new CustomEvent('currencyChanged', {
            detail: { currency: 'USD' },
            bubbles: true
        });
        document.dispatchEvent(event);

        console.log('💱 Currency change event dispatched for USD');

        // Wait a bit and try EUR
        setTimeout(() => {
            const eventEUR = new CustomEvent('currencyChanged', {
                detail: { currency: 'EUR' },
                bubbles: true
            });
            document.dispatchEvent(eventEUR);
            console.log('💱 Currency change event dispatched for EUR');
        }, 2000);

    } catch (error) {
        console.error('Test error:', error);
    }
};

// Run the test
testCurrency();
