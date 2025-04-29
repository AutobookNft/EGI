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
