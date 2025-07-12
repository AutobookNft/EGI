import { getTranslation } from '../../js/utils/translations';

export class AssistantActions {
    static handleCreateArtwork() {
        alert(getTranslation('assistant.create_artwork'));
    }
    static handleBuyArtwork() {
        alert(getTranslation('assistant.buy_artwork'));
    }
    static handleWhatIsEGI() {
        alert(getTranslation('assistant.what_is_egi'));
    }
    static handleGuidedTour() {
        alert(getTranslation('assistant.guided_tour'));
    }
    static handleCustomEGI() {
        alert(getTranslation('assistant.custom_egi'));
    }
    static handleWhitePaper() {
        alert(getTranslation('assistant.white_paper'));
    }
    static handleLetMeGuide() {
        alert(getTranslation('assistant.let_me_guide'));
    }
}
