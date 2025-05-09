// resources/ts/main_app_global_types.d.ts

// Interfaccia per FileUploadManagerGlobal
interface FileUploadManagerGlobal {
    initUploadForm: (selector: string) => void;
    resetUploadForm: () => void;
  }

  // Interfaccia per traduzioni globali (esempio)
  interface GlobalTranslations {
    [key: string]: string | GlobalTranslations;
  }

  // Interfaccia per limiti di upload (esempio)
  interface GlobalUploadLimits {
    maxSize: number;
    allowedExtensions: string[];
  }

  // Dichiarazione per la variabile globale droppedFiles
  declare var droppedFiles: FileList | null;

  // Estensione dell'interfaccia Window
  interface Window {
    uploadType: string;
    droppedFiles: FileList | null;
    imagesPath: string;
    assetBaseUrl: string;
    fileUploadManager?: FileUploadManagerGlobal;
    globalModalManager?: ModalManager; // Aggiunto per gestire le modali
    Swal?: any; // SweetAlert2
    $?: any; // jQuery
    jQuery?: any; // jQuery
    allowedExtensions: string[];
    maxSize: number;
    translations: GlobalTranslations;
    uploadLimits: GlobalUploadLimits;
    Echo: any; // Laravel Echo
    scanvirus: any; // Oggetto scanvirus
  }

  /**
     * Interface for modal elements used by ModalManager.
     * @interface ModalElements
     */
    interface ModalElements {
        modal: HTMLElement | null;
        openButtons: NodeListOf<HTMLElement>; // Modificato per gestire più bottoni
        returnButton: HTMLElement | null;
        modalContent: HTMLElement | null;
    }

    // Estendi l'interfaccia Window per aggiungere globalModalManager
    declare global {
        interface Window {
            globalModalManager?: ModalManager; // Istanza globale (opzionale)
            uploadType?: string; // Tipo di upload corrente
            fileUploadManager?: any; // Riferimento al gestore upload (se esiste globalmente)
            redirectToURL?: () => void; // Funzione redirect (se esiste globalmente)
        }
    }
