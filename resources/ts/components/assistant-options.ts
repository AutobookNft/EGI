import { AssistantActions } from './assistant-actions';

export interface AssistantOption {
    key: string;
    label: string;  // Torniamo a stringa
    description?: string;
    action: () => void;
}

export const assistantOptions: AssistantOption[] = [
    {
        key: 'assistant.create_egi_contextual',
        label: 'assistant.create_egi_contextual',  // Solo la chiave
        action: AssistantActions.handleCreateEgiContextual
    },
    {
        key: 'create_artwork',
        label: 'assistant.create_artwork',
        action: AssistantActions.handleCreateArtwork
    },
    {
        key: 'buy_artwork',
        label: 'assistant.buy_artwork',
        action: AssistantActions.handleBuyArtwork
    },
    {
        key: 'what_is_egi',
        label: 'assistant.what_is_egi',
        action: AssistantActions.handleWhatIsEGI
    },
    {
        key: 'guided_tour',
        label: 'assistant.guided_tour',
        action: AssistantActions.handleGuidedTour
    },
    {
        key: 'custom_egi',
        label: 'assistant.custom_egi',
        action: AssistantActions.handleCustomEGI
    },
    {
        key: 'white_paper',
        label: 'assistant.white_paper',
        action: AssistantActions.handleWhitePaper
    },
    {
        key: 'let_me_guide',
        label: 'assistant.let_me_guide',
        action: AssistantActions.handleLetMeGuide
    }
];
