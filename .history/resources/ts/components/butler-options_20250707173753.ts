/**
 * @package Resources\Ts\Components
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Butler Accordion Categories)
 * @date 2025-07-07
 * @purpose Opzioni maggiordomo Natan con categorie accordion per guidare l'utente
 */

import { getTranslation } from '../../js/utils/translations';
import { AssistantActions } from './assistant-actions';

export interface ButlerSubOption {
    key: string;
    icon: string;
    label: string; // chiave di traduzione
    description: string; // chiave di traduzione
    action: () => void;
}

export interface ButlerCategory {
    key: string;
    icon: string;
    label: string; // chiave di traduzione
    description: string; // chiave di traduzione
    isExpanded?: boolean;
    subOptions: ButlerSubOption[];
}

// === AZIONI PER CATEGORIE "CAPIRE DOVE SONO" ===
const handleWhatIsFlorenceEGI = () => {
    // Scroll alla sezione hero o naviga a pagina about
    const heroSection = document.querySelector('#hero-section');
    if (heroSection) {
        heroSection.scrollIntoView({ behavior: 'smooth' });
        // Evidenzia il claim principale
        setTimeout(() => {
            const claim = heroSection.querySelector('h1, .hero-title');
            if (claim) {
                claim.classList.add('natan-highlight-pulse');
                setTimeout(() => claim.classList.remove('natan-highlight-pulse'), 3000);
            }
        }, 500);
    } else {
        // Fallback: naviga a pagina under construction
        window.location.href = '/under-construction/what-is-florenceegi';
    }
};

const handleWhatAreEGIs = () => {
    // Spotlight sulla prima collezione EGI
    const firstCollection = document.querySelector('.collection-card-nft:first-child');
    if (firstCollection) {
        firstCollection.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => {
            // Crea un tooltip esplicativo
            AssistantActions.createExplanationTooltip(
                firstCollection as HTMLElement,
                'Questo è un EGI: un certificato digitale che unisce arte, impatto ambientale e proprietà reale',
                4000
            );
        }, 1000);
    } else {
        window.location.href = '/under-construction/what-are-egis';
    }
};

const handleWhyCantBuyEGIs = () => {
    // Naviga alla pagina dedicata (da creare)
    window.location.href = '/why-cant-buy-egis';
};

// === AZIONI PER CATEGORIE "INIZIARE SUBITO" ===
const handleCreateEGI = () => {
    // Riusa l'azione esistente per creare EGI
    AssistantActions.handleCreateEgiContextual();
};

const handleReserveEGI = () => {
    // Spotlight sui pulsanti di prenotazione EGI
    const reservationButton = document.querySelector('[data-action="reserve"], .reservation-button, .btn-reserve');
    if (reservationButton) {
        reservationButton.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => {
            AssistantActions.createExplanationTooltip(
                reservationButton as HTMLElement,
                'Prenota un EGI per esprimere il tuo interesse e ricevere priorità quando sarà disponibile',
                4000
            );
        }, 1000);
    } else {
        window.location.href = '/under-construction/reservations';
    }
};

const handleCreateCollection = () => {
    // Naviga al creator delle collezioni
    window.location.href = '/collections/create';
};

// === AZIONI PER CATEGORIE "CONOSCERE I PROTAGONISTI" ===
const handleDiscoverArchetypes = () => {
    // Per ora va a under construction, in futuro a pagina archetipi completa
    window.location.href = '/under-construction/archetypes';
};

const handleBecomePatron = () => {
    // Va alla pagina mecenati esistente
    window.location.href = '/archetypes/patron';
};

const handleBecomeCollector = () => {
    // Per ora under construction
    window.location.href = '/under-construction/collector';
};

// === AZIONI PER IDENTITÀ NARRATIVA ===
const handleWhoIsNatan = () => {
    // Mostra modal con storia di Natan
    AssistantActions.showNatanStoryModal();
};

const handleFlorenceStory = () => {
    // Naviga alla storia di FlorenceEGI
    window.location.href = '/under-construction/florence-story';
};

// === AZIONI PER "MI SENTO PERSO" ===
const handleGuidedTour = () => {
    // Avvia tour guidato interattivo
    AssistantActions.startGuidedTour();
};

const handlePersonalAssistant = () => {
    // Apre chat assistenza personalizzata
    AssistantActions.openPersonalAssistance();
};

// === CONFIGURAZIONE CATEGORIE ACCORDION ===
export const butlerCategories: ButlerCategory[] = [
    {
        key: 'understand',
        icon: '👁️',
        label: 'assistant.category_understand',
        description: 'assistant.category_understand_desc',
        isExpanded: false,
        subOptions: [
            {
                key: 'what_is_florenceegi',
                icon: '🏛️',
                label: 'assistant.what_is_florenceegi',
                description: 'assistant.what_is_florenceegi_desc',
                action: handleWhatIsFlorenceEGI
            },
            {
                key: 'what_are_egis',
                icon: '🎨',
                label: 'assistant.what_are_egis',
                description: 'assistant.what_are_egis_desc',
                action: handleWhatAreEGIs
            },
            {
                key: 'why_cant_buy_egis',
                icon: '🚧',
                label: 'assistant.why_cant_buy_egis',
                description: 'assistant.why_cant_buy_egis_desc',
                action: handleWhyCantBuyEGIs
            }
        ]
    },
    {
        key: 'start',
        icon: '🚀',
        label: 'assistant.category_start',
        description: 'assistant.category_start_desc',
        isExpanded: false,
        subOptions: [
            {
                key: 'create_egi',
                icon: '✨',
                label: 'assistant.create_egi',
                description: 'assistant.create_egi_desc',
                action: handleCreateEGI
            },
            {
                key: 'reserve_egi',
                icon: '📋',
                label: 'assistant.reserve_egi',
                description: 'assistant.reserve_egi_desc',
                action: handleReserveEGI
            },
            {
                key: 'create_collection',
                icon: '🖼️',
                label: 'assistant.create_collection',
                description: 'assistant.create_collection_desc',
                action: handleCreateCollection
            }
        ]
    },
    {
        key: 'explore',
        icon: '🧭',
        label: 'assistant.category_explore',
        description: 'assistant.category_explore_desc',
        isExpanded: false,
        subOptions: [
            {
                key: 'discover_archetypes',
                icon: '👥',
                label: 'assistant.discover_archetypes',
                description: 'assistant.discover_archetypes_desc',
                action: handleDiscoverArchetypes
            },
            {
                key: 'become_patron',
                icon: '👑',
                label: 'assistant.become_patron',
                description: 'assistant.become_patron_desc',
                action: handleBecomePatron
            },
            {
                key: 'become_collector',
                icon: '💎',
                label: 'assistant.become_collector',
                description: 'assistant.become_collector_desc',
                action: handleBecomeCollector
            }
        ]
    },
    {
        key: 'identity',
        icon: '🎭',
        label: 'assistant.category_identity',
        description: 'assistant.category_identity_desc',
        isExpanded: false,
        subOptions: [
            {
                key: 'who_is_natan',
                icon: '🎩',
                label: 'assistant.who_is_natan',
                description: 'assistant.who_is_natan_desc',
                action: handleWhoIsNatan
            },
            {
                key: 'florence_story',
                icon: '🏛️',
                label: 'assistant.florence_story',
                description: 'assistant.florence_story_desc',
                action: handleFlorenceStory
            }
        ]
    },
    {
        key: 'lost',
        icon: '🤝',
        label: 'assistant.category_lost',
        description: 'assistant.category_lost_desc',
        isExpanded: false,
        subOptions: [
            {
                key: 'guided_tour',
                icon: '🗺️',
                label: 'assistant.guided_tour',
                description: 'assistant.guided_tour_desc',
                action: handleGuidedTour
            },
            {
                key: 'personal_assistant',
                icon: '💬',
                label: 'assistant.personal_assistant',
                description: 'assistant.personal_assistant_desc',
                action: handlePersonalAssistant
            }
        ]
    }
];

// === BACKWARD COMPATIBILITY ===
// Mantieni le opzioni flat esistenti per retrocompatibilità
export const butlerOptions = butlerCategories.flatMap(category =>
    category.subOptions.map(subOption => ({
        key: subOption.key,
        icon: subOption.icon,
        label: subOption.label,
        description: subOption.description,
        action: subOption.action
    }))
);
