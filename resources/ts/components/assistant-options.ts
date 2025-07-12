import { AssistantActions } from './assistant-actions';
import { getTranslation } from '../../js/utils/translations';

export interface AssistantOption {
    key: string;
    label: string;
    description?: string;
    action: () => void;
}

export const assistantOptions: AssistantOption[] = [
    {
        key: 'create_egi_contextual',
        label: getTranslation('assistant.create_egi_contextual'),
        action: AssistantActions.handleCreateEgiContextual
    },
    {
        key: 'create_artwork',
        label: getTranslation('assistant.create_artwork'),
        action: AssistantActions.handleCreateArtwork
    },
    {
        key: 'buy_artwork',
        label: getTranslation('assistant.buy_artwork'),
        action: AssistantActions.handleBuyArtwork
    },
    {
        key: 'what_is_egi',
        label: getTranslation('assistant.what_is_egi'),
        action: AssistantActions.handleWhatIsEGI
    },
    {
        key: 'guided_tour',
        label: getTranslation('assistant.guided_tour'),
        action: AssistantActions.handleGuidedTour
    },
    {
        key: 'custom_egi',
        label: getTranslation('assistant.custom_egi'),
        action: AssistantActions.handleCustomEGI
    },
    {
        key: 'white_paper',
        label: getTranslation('assistant.white_paper'),
        action: AssistantActions.handleWhitePaper
    },
    {
        key: 'let_me_guide',
        label: getTranslation('assistant.let_me_guide'),
        action: AssistantActions.handleLetMeGuide
    }
];
