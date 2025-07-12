import { getTranslation } from '../../js/utils/translations';
import { AssistantActions } from './assistant-actions';

export interface ButlerOption {
    key: string;
    icon: string;
    label: string; // chiave di traduzione
    description: string; // chiave di traduzione
    action: () => void;
}

// Azioni placeholder: da collegare alle vere funzioni centralizzate
const handleExplore = () => {
    window.location.href = '/home/collections';
};
const handleLearn = () => {
    const impactSection = document.querySelector('.nft-stats-section');
    if (impactSection) {
        impactSection.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => {
            // spotlight
        }, 1000);
    }
};
const handleStart = () => {
    // spotlight sui pulsanti di registrazione
};
const handleBusiness = () => {
    const creatorSection = document.querySelector('section[aria-labelledby="creator-cta-heading"]');
    if (creatorSection) {
        creatorSection.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => {
            // spotlight
        }, 1000);
    }
};
const handleCreateEGI = AssistantActions.handleCreateEgiContextual;

export const butlerOptions: ButlerOption[] = [
    {
        key: 'explore',
        icon: '🔍',
        label: 'assistant.explore',
        description: 'assistant.explore_desc',
        action: handleExplore
    },
    {
        key: 'learn',
        icon: '🌱',
        label: 'assistant.learn',
        description: 'assistant.learn_desc',
        action: handleLearn
    },
    {
        key: 'start',
        icon: '🚀',
        label: 'assistant.start',
        description: 'assistant.start_desc',
        action: handleStart
    },
    {
        key: 'business',
        icon: '💼',
        label: 'assistant.business',
        description: 'assistant.business_desc',
        action: handleBusiness
    },
    {
        key: 'create_egi',
        icon: '✨',
        label: 'assistant.create_egi',
        description: 'assistant.create_egi_desc',
        action: handleCreateEGI
    }
];
