<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Error Messages - Italian
    |--------------------------------------------------------------------------
    */

    'dev' => [
        // == Existing Entries ==
        'authentication_error' => 'Tentativo di accesso non autenticato.',
        'scan_error' => 'Si è verificato un errore durante la scansione antivirus per il file: :filename.',
        'virus_found' => 'È stato rilevato un virus nel file: :filename.',
        'invalid_file_extension' => 'Il file ha un\'estensione non valida (:extension).',
        'max_file_size' => 'Il file (:size) supera la dimensione massima consentita (:max_size).',
        'invalid_file_pdf' => 'Il file PDF fornito non è valido o corrotto.',
        'mime_type_not_allowed' => 'Il tipo MIME del file (:mime) non è consentito.',
        'invalid_image_structure' => 'La struttura del file immagine non è valida.',
        'invalid_file_name' => 'Nome file non valido ricevuto durante il processo di upload: :filename.',
        'error_getting_presigned_url' => 'Si è verificato un errore durante il recupero dell\'URL presigned per :object.',
        'error_during_file_upload' => 'Si è verificato un errore durante il processo di caricamento del file per :filename.',
        'unable_to_save_bot_file' => 'Impossibile salvare il file per il bot: :filename.',
        'unable_to_create_directory' => 'Impossibile creare la directory per il caricamento del file: :directory.',
        'unable_to_change_permissions' => 'Impossibile modificare i permessi per il file/directory: :path.',
        'impossible_save_file' => 'È stato impossibile salvare il file: :filename sul disco :disk.',
        'error_during_create_egi_record' => 'Si è verificato un errore durante la creazione del record EGI nel database.',
        'error_during_file_name_encryption' => 'Si è verificato un errore durante il processo di crittografia del nome del file.',
        'imagick_not_available' => 'L\'estensione PHP Imagick non è disponibile o configurata correttamente.',
        'json_error' => 'Errore di elaborazione JSON. Tipo: :type, Messaggio: :message',
        'generic_server_error' => 'Si è verificato un errore generico del server. Dettagli: :details',
        'file_not_found' => 'Il file richiesto non è stato trovato: :path.',
        'unexpected_error' => 'Errore imprevisto nel sistema. Controllare i log per dettagli.',
        'error_deleting_local_temp_file' => 'Impossibile eliminare il file temporaneo locale: :path.',
        'acl_setting_error' => 'Si è verificato un errore durante l\'impostazione dell\'ACL (:acl) per l\'oggetto :object.',
        'invalid_input' => 'Input non valido fornito per il parametro :param.',
        'temp_file_not_found' => 'File temporaneo non trovato al percorso: :path.',
        'error_deleting_ext_temp_file' => 'Impossibile eliminare il file temporaneo esterno: :path.',
        'ucm_delete_failed' => 'Impossibile eliminare la configurazione con chiave :key: :message',
        'undefined_error_code' => 'Codice di errore non definito incontrato: :errorCode. Codice originale era [:_original_code].',
        'fallback_error' => 'Si è verificato un errore ma non è stata trovata alcuna configurazione specifica per il codice [:_original_code].',
        'fatal_fallback_failure' => 'ERRORE FATALE: Configurazione di fallback mancante o non valida. Il sistema non può rispondere.',
        'ucm_audit_not_found' => 'Nessun record di audit trovato per l\'ID di configurazione specificato: :id.',
        'ucm_duplicate_key' => 'Tentativo di creare una configurazione con una chiave duplicata: :key.',
        'ucm_create_failed' => 'Creazione voce di configurazione fallita: :key. Motivo: :reason',
        'ucm_update_failed' => 'Aggiornamento voce di configurazione fallita: :key. Motivo: :reason',
        'ucm_not_found' => 'Chiave di configurazione non trovata: :key.',
        'invalid_file' => 'File fornito non valido: :reason',
        'invalid_file_validation' => 'Validazione file fallita per il campo :field. Motivo: :reason',
        'error_saving_file_metadata' => 'Salvataggio metadati fallito per file ID :file_id. Motivo: :reason',
        'server_limits_restrictive' => 'I limiti del server potrebbero essere troppo restrittivi. Controllare :limit_name (:limit_value).',
        'egi_auth_required' => 'Autenticazione richiesta per l\'upload dell\'EGI.',
        'egi_file_input_error' => "Input 'file' mancante o non valido. Codice errore upload: :code",
        'egi_validation_failed' => 'Validazione metadati EGI fallita. Controllare errori di validazione nel contesto.',
        'egi_collection_init_error' => 'Errore critico inizializzando la collection di default per l\'utente :user_id.',
        'egi_crypto_error' => 'Fallita la cifratura del nome file: :filename',
        'egi_db_error' => 'Errore database processando l\'EGI :egi_id per la collection :collection_id.',
        'egi_storage_critical_failure' => 'Fallimento critico nel salvataggio del file EGI :egi_id sul/i disco/hi: :disks',
        'egi_storage_config_error' => "Il disco di storage 'local' richiesto per il fallback non è configurato.",
        'egi_unexpected_error' => 'Errore inaspettato durante l\'elaborazione dell\'EGI per il file :original_filename.',
        'egi_unauthorized_access' => 'Tentativo non autenticato di accedere alla pagina di upload EGI.',
        'record_not_found_egi_in_reservation_controller' => 'Nessun record EGI trovato per l\'ID :egi_id nella prenotazione. Verificare che l\'EGI esista e sia accessibile.',
        // Errori relativi all'interfaccia utente (messaggi per sviluppatori)
        'egi_page_access_notice' => 'Pagina di upload EGI acceduta con successo dall\'amministratore con ID :user_id.',
        'egi_page_rendering_error' => 'Eccezione durante il rendering della pagina di upload EGI: :exception_message',
        'egi_update_failed' => 'Aggiornamento EGI fallito: :error per utente :user_id su EGI :egi_id',
        'egi_delete_failed' => 'Eliminazione EGI fallita: :error per utente :user_id su EGI :egi_id',

        // Errori di validazione (messaggi per sviluppatori)
        'invalid_egi_file' => 'Validazione del file EGI fallita con errori: :validation_errors',

        // Errori di elaborazione (messaggi per sviluppatori)
        'error_during_egi_processing' => 'Errore durante l\'elaborazione del file EGI nella fase ":processing_stage": :exception_message',

        // Errori di creazione Wallet (messaggi per sviluppatori)
        'wallet_creation_failed' => 'Impossibile creare il wallet per la collezione :collection_id, utente :user_id: :error_message',
        'wallet_quota_check_error' => 'Errore durante il controllo della quota del wallet per l\'utente :user_id, collezione :collection_id: :error_message',
        'wallet_insufficient_quota' => 'L\'utente :user_id ha quota insufficiente per la collezione :collection_id. Richiesto: mint=:required_mint_quota, rebind=:required_rebind_quota. Disponibile: mint=:current_mint_quota, rebind=:current_rebind_quota.',
        'wallet_address_invalid' => 'Formato dell\'indirizzo wallet non valido per l\'utente :user_id: :wallet_address',
        'wallet_not_found' => 'Wallet non trovato per l\'utente :user_id e la collezione :collection_id',
        'wallet_already_exists' => 'Il wallet esiste già per l\'utente :user_id e la collezione :collection_id con ID :wallet_id',
        'wallet_invalid_secret' => 'Chiave segreta non valida fornita per wallet :wallet da IP :ip',
        'wallet_validation_failed' => 'Validazione wallet fallita. Errori: :errors',
        'wallet_connection_failed' => 'Impossibile stabilire connessione wallet. Errore: :message',
        'wallet_disconnect_failed' => 'Impossibile disconnettere il wallet. Errore: :error',

        // COLLECTION_CREATION_FAILED
        'collection_creation_failed' => 'Impossibile creare la collection predefinita per l\'utente :user_id. Dettagli errore: :error_details',

        // COLLECTION_FIND_CREATE_FAILED
        'collection_find_create_failed' => 'Impossibile trovare o creare la collection per l\'utente :user_id. Dettagli errore: :error_details',

         // Errori aggiornamento collezione corrente utente
        'user_current_collection_update_failed' => 'Errore critico durante aggiornamento current_collection_id per utente :user_id verso collezione :collection_id. Operazione database fallita: :error_message. Questo impedisce la corretta associazione user-collection nel workflow FlorenceEGI.',
        'user_current_collection_validation_failed' => 'Validazione fallita durante aggiornamento collezione corrente per utente :user_id e collezione :collection_id. Tipo validazione: :validation_type. Errore: :validation_error. Questo indica problemi di integrità dati che devono essere risolti.',

        // == New Entries ==
        'authorization_error' => 'Autorizzazione negata per l\'azione richiesta: :action.',
        'csrf_token_mismatch' => 'Token CSRF non valido o scaduto.',
        'route_not_found' => 'Il percorso o la risorsa richiesta non è stata trovata: :url.',
        'method_not_allowed' => 'Metodo HTTP :method non consentito per questo percorso: :url.',
        'too_many_requests' => 'Troppe richieste rilevate dal limitatore di frequenza.',
        'database_error' => 'Si è verificato un errore di query o connessione al database. Dettagli: :details',
        'record_not_found' => 'Il record richiesto dal database non è stato trovato (Modello: :model, ID: :id).',
        'validation_error' => 'Validazione input fallita. Controllare il contesto per errori specifici.', // Messaggio dev generico
        'utm_load_failed' => 'Caricamento file traduzioni fallito: :file per la lingua :locale.',
        'utm_invalid_locale' => 'Tentativo di usare una lingua non valida o non supportata: :locale.',
        'uem_email_send_failed' => 'EmailNotificationHandler: invio notifica fallito per :errorCode. Motivo: :reason',
        'uem_slack_send_failed' => 'SlackNotificationHandler: invio notifica fallito per :errorCode. Motivo: :reason',
        'uem_recovery_action_failed' => 'Azione di recupero :action fallita per errore :errorCode. Motivo: :reason',
        'user_unauthenticated_access' => 'Utente non autenticato: Tentativo di accesso a una risorsa protetta senza autenticazione valida. ID Collezione Target (se applicabile): :target_collection_id. IP: :ip_address.',
        'set_current_collection_forbidden' => 'Accesso Negato: L\'Utente ID :user_id ha tentato di impostare la Collezione ID :collection_id come corrente senza autorizzazione. IP: :ip_address.',
        'set_current_collection_failed' => 'Errore Database: Impossibile aggiornare la collezione corrente per l\'Utente ID :user_id alla Collezione ID :collection_id. Dettagli: :exception_message.',
        'auth_required' => 'Autenticazione richiesta per eseguire questa azione. Utente non connesso.',
        'auth_required_for_like' => 'L\'utente deve essere autenticato per mettere "mi piace" agli elementi. Stato autenticazione corrente: :status',
        'like_toggle_failed' => 'Impossibile attivare/disattivare il "mi piace" per :resource_type :resource_id. Errore: :error',

        // Dev message for reservations system
        'reservation_egi_not_available' => 'L\'EGI con ID :egi_id non è disponibile per la prenotazione. Potrebbe essere già stato coniato o non pubblicato.',
        'reservation_amount_too_low' => 'L\'importo offerto di :amount EUR è inferiore al minimo richiesto per questo EGI.',
        'reservation_unauthorized' => 'Tentativo non autorizzato di prenotare l\'EGI :egi_id. L\'utente deve essere autenticato o avere un wallet connesso.',
        'reservation_certificate_generation_failed' => 'Impossibile generare il certificato per la prenotazione :reservation_id. Errore: :error',
        'reservation_certificate_not_found' => 'Certificato con UUID :uuid non trovato.',
        'reservation_already_exists' => 'L\'utente ha già una prenotazione attiva per l\'EGI :egi_id.',
        'reservation_cancel_failed' => 'Impossibile annullare la prenotazione :id. Errore: :error',
        'reservation_unauthorized_cancel' => 'Tentativo non autorizzato di annullare la prenotazione :id. Solo il proprietario può annullare.',
        'reservation_status_failed' => 'Impossibile recuperare lo stato della prenotazione per l\'EGI :egi_id. Errore: :error',
        'reservation_unknown_error' => 'Si è verificato un errore sconosciuto durante il processo di prenotazione. Errore: :error',

        // Dev message for statistics system
        'statistics_calculation_failed' => 'Calcolo statistiche fallito per utente :user_id. Contesto: :error_context. Errore: :error_message',
        'icon_not_found' => 'Icona :icon_name con stile :style non trovata nel database. Uso icona di fallback.',
        'icon_retrieval_failed' => 'Impossibile recuperare icona :icon_name. Errore: :error_message. Uso icona di fallback.',
        'statistics_cache_clear_failed' => 'Impossibile pulire cache statistiche per utente :user_id. Errore: :error_message',
        'statistics_summary_failed' => 'Impossibile calcolare riassunto statistiche per utente :user_id. Errore: :error_message',

        // EGI Upload Handler Service-Based Architecture
        'egi_collection_service_error' => 'Errore del servizio Collection durante upload EGI: :error_details. Operazione: :operation_id',
        'egi_wallet_service_error' => 'Errore del servizio Wallet durante setup collection: :error_details. Collection ID: :collection_id',
        'egi_role_service_error' => 'Errore del servizio UserRole durante assegnazione ruolo: :error_details. User ID: :user_id',
        'egi_service_integration_error' => 'Errore di integrazione tra servizi EGI: :error_details. Services: :services_involved',
        'egi_enhanced_authentication_error' => 'Errore autenticazione avanzata EGI: :auth_type fallita. Session: :session_data',
        'egi_file_input_validation_error' => 'Errore validazione input file EGI: :validation_error. File: :original_filename',
        'egi_metadata_validation_error' => 'Errore validazione metadata EGI: :validation_errors. Request data: :request_data',
        'egi_data_preparation_error' => 'Errore preparazione dati EGI: :error_details. File: :original_filename',
        'egi_record_creation_error' => 'Errore creazione record EGI nel database: :error_details. Collection: :collection_id',
        'egi_file_storage_error' => 'Errore salvataggio file EGI: :error_details. Storage disks: :failed_disks',
        'egi_cache_invalidation_error' => 'Errore invalidazione cache EGI: :error_details. Cache keys: :cache_keys',

        // Collection Service Enhanced
        'collection_creation_enhanced_error' => 'Errore creazione collection avanzata: :error_details. User: :user_id, Nome: :collection_name',
        'collection_validation_error' => 'Errore validazione collection: :validation_error. User: :user_id',
        'collection_limit_exceeded_error' => 'Limite collection superato per user :user_id. Attuali: :current_count, Max: :max_limit',
        'collection_wallet_attachment_failed' => 'Fallimento attachment wallet a collection :collection_id: :error_details',
        'collection_role_assignment_failed' => 'Fallimento assegnazione ruolo creator a user :user_id: :error_details',
        'collection_ownership_mismatch_error' => 'Mismatch ownership collection :collection_id. Owner: :actual_owner, Expected: :expected_owner',
        'collection_current_update_error' => 'Errore aggiornamento current_collection_id per user :user_id: :error_details',

        // Enhanced Storage
        'egi_storage_disk_config_error' => 'Errore configurazione disco storage :disk_name: :error_details',
        'egi_storage_emergency_fallback_failed' => 'Fallimento fallback emergenza storage: :error_details. Tutti i dischi falliti: :failed_disks',
        'egi_temp_file_read_error' => 'Errore lettura file temporaneo :temp_path: :error_details',

        // Enhanced Authentication & Session
        'egi_session_auth_invalid' => 'Autenticazione session EGI non valida. Session status: :session_status, User ID: :user_id',
        'egi_wallet_auth_mismatch' => 'Mismatch wallet autenticazione. Session wallet: :session_wallet, User wallet: :user_wallet',

         // Enhanced Registration Errors
        'enhanced_registration_failed' => 'Fallimento registrazione avanzata con setup ecosistema: :error. User ID: :user_id, Collection ID: :collection_id, Components: :partial_creation',
        'registration_user_creation_failed' => 'Fallimento creazione utente durante registrazione: :error. Email: :email, User type: :user_type',
        'registration_collection_creation_failed' => 'Fallimento creazione collezione default durante registrazione: :error. User ID: :user_id, Collection name: :collection_name',
        'registration_wallet_setup_failed' => 'Fallimento setup wallet durante registrazione: :error. User: :user_id, Collection: :collection_id',
        'registration_role_assignment_failed' => 'Fallimento assegnazione ruoli durante registrazione: :error. User: :user_id, User type: :user_type',
        'registration_gdpr_consent_failed' => 'Fallimento processing consensi GDPR durante registrazione: :error. User: :user_id, Consents: :consents',
        'registration_ecosystem_setup_incomplete' => 'Setup ecosistema incompleto durante registrazione: :error. User: :user_id, Completed steps: :completed_steps',
        'registration_validation_enhanced_failed' => 'Validazione registrazione avanzata fallita: :validation_errors. Request data: :request_data',
        'registration_user_type_invalid' => 'Tipo utente non valido durante registrazione: :user_type. Valid types: creator,mecenate,acquirente,azienda',
        'registration_rate_limit_exceeded' => 'Rate limit registrazione superato. IP: :ip_address, Attempts: :attempts, Time window: :time_window',
        'registration_page_load_error' => 'Errore caricamento pagina registrazione: :error. IP: :ip_address',
        'permission_based_registration_failed' => 'Errore durante la registrazione basata sui permessi. Dettagli: :error',
        'algorand_wallet_generation_failed' => 'Impossibile generare indirizzo wallet Algorand valido. Errore: :error',
        'ecosystem_setup_failed' => 'Errore durante la creazione dell\'ecosistema utente (collection, wallet, relazioni). Dettagli: :error',
        'user_domain_initialization_failed' => 'Errore durante l\'inizializzazione dei domini utente (profile, personal_data, etc.). Dettagli: :error',
        'gdpr_consent_processing_failed' => 'Errore durante l\'elaborazione dei consensi GDPR. Dettagli: :error',
        'role_assignment_failed' => 'Impossibile assegnare il ruolo :role al nuovo utente. Dettagli: :error',
        'personal_data_view_failed' => 'Si è verificato un errore nel caricamento dei tuoi dati personali. Per favore riprova tra qualche minuto o contatta il supporto se il problema persiste.',
        'personal_data_update_failed' => 'Non è stato possibile salvare le modifiche ai tuoi dati personali. Verifica che tutti i campi siano compilati correttamente e riprova.',
        'personal_data_export_failed' => 'Si è verificato un errore durante l\'esportazione dei tuoi dati. Riprova più tardi o contatta il supporto per assistenza.',
        'personal_data_deletion_failed' => 'Non è stato possibile elaborare la richiesta di cancellazione dei tuoi dati. Ti preghiamo di contattare il nostro supporto per ricevere assistenza immediata.',
        'gdpr_export_rate_limit' => 'Puoi richiedere un\'esportazione dei tuoi dati una volta ogni 30 giorni. La prossima esportazione sarà disponibile tra qualche giorno.',
        'gdpr_violation_attempt' => 'GDPR violation attempt detected. Check consent logic in PersonalDataController, user consent status and UpdatePersonalDataRequest validation.',
        'gdpr_notification_send_failed' => 'Errore critico durante l\'invio di una notifica GDPR. Controllare la configurazione del servizio di notifica e i log per dettagli.',
        'gdpr_notification_dispatch_failed' => 'Errore critico durante l\'invio della notifica GDPR. Verifica la configurazione dei handler e la validità dei dati di input.',
        'gdpr_notification_persistence_failed' => 'Errore critico durante la persistenza della notifica GDPR nel database. Transazione fallita, possibile problema di integrità dei dati.',
        'gdpr_service_unavailable' => 'ConsentService GDPR o dipendenze correlate non disponibili. Verificare database, DTO e traduzioni.',

        // GDPR Consent Errors - Developer Messages IT
        'gdpr_consent_required' => 'Consenso GDPR richiesto ma non fornito. Verificare ConsentService::hasConsent() e middleware consent.',
        'gdpr_consent_update_error' => 'Errore aggiornamento consensi utente. Controllare ConsentService::updateUserConsents() e validazione form.',
        'gdpr_consent_save_error' => 'Fallimento salvataggio consensi in database. Verificare transazione DB e constraints UserConsent model.',
        'gdpr_consent_load_error' => 'Errore caricamento stato consensi utente. Controllare ConsentService::getUserConsentStatus() e relazioni model.',

        // GDPR Export Errors - Developer Messages IT
        'gdpr_export_request_error' => 'Errore richiesta esportazione dati GDPR. Verificare DataExportService e validazione request.',
        'gdpr_export_limit_reached' => 'Limite esportazioni GDPR raggiunto. Controllare rate limiting e politiche esportazione.',
        'gdpr_export_create_error' => 'Fallimento creazione file esportazione. Verificare DataExportService::processExport() e storage permissions.',
        'gdpr_export_download_error' => 'Errore download file esportazione. Controllare file existence, permissions e URL generation.',
        'gdpr_export_status_error' => 'Errore verifica stato esportazione. Verificare DataExport model e status tracking.',
        'gdpr_export_processing_failed' => 'Fallimento elaborazione dati esportazione. Controllare background jobs e data serialization.',

        // GDPR Processing Restriction Errors - Developer Messages IT
        'gdpr_processing_restricted' => 'Operazione bloccata da restrizione processing GDPR. Verificare ProcessingRestrictionService.',
        'gdpr_processing_limit_view_error' => 'Errore caricamento vista limitazioni processing. Controllare middleware e view data.',
        'gdpr_processing_restriction_create_error' => 'Fallimento creazione restrizione processing. Verificare ProcessingRestrictionService::createRestriction().',
        'gdpr_processing_restriction_remove_error' => 'Errore rimozione restrizione processing. Controllare permissions e validation logic.',
        'gdpr_processing_restriction_limit_reached' => 'Limite restrizioni processing raggiunto. Verificare business rules e rate limiting.',

        // GDPR Deletion Errors - Developer Messages IT
        'gdpr_deletion_request_error' => 'Errore richiesta cancellazione account GDPR. Controllare AccountDeletionService e validazione.',
        'gdpr_deletion_cancellation_error' => 'Fallimento cancellazione richiesta deletion. Verificare status transitions e business logic.',
        'gdpr_deletion_processing_error' => 'Errore elaborazione cancellazione account. Controllare background jobs e data cleanup.',

        // GDPR Breach Report Errors - Developer Messages IT
        'gdpr_breach_report_error' => 'Errore segnalazione violazione GDPR. Verificare BreachReportService e form validation.',
        'gdpr_breach_evidence_upload_error' => 'Fallimento upload evidenze breach. Controllare file upload service e storage.',

        // GDPR Activity Log Errors - Developer Messages IT
        'gdpr_activity_log_error' => 'Errore registrazione attività GDPR. Verificare ActivityLogService e database logging.',

        // GDPR Security Errors - Developer Messages IT
        'gdpr_enhanced_security_required' => 'Autenticazione potenziata richiesta per operazione GDPR. Verificare security middleware.',
        'gdpr_critical_security_required' => 'Conferma password richiesta per operazione critica GDPR. Controllare auth verification.',

        // My Added Errors - Developer Messages IT
        'gdpr_consent_page_failed' => 'Errore caricamento pagina consensi GDPR. Verificare ConsentService, DTO integration e view data structure.',
        'gdpr_service_unavailable' => 'ConsentService GDPR o dipendenze non disponibili. Controllare database connection, DTO files e traduzioni.',

        'legal_content_load_failed' => 'Caricamento del contenuto legale fallito. Controllare i permessi dei file in resources/legal/ e la validità del symlink "current".',
        'terms_acceptance_check_failed' => 'Fallimento nella verifica dell\'accettazione dei termini correnti per l\'utente. Controllare la logica in ConsentService e la raggiungibilità del LegalContentService.',

        // Registration validation errors - Messaggi sviluppatore
        'registration_email_already_exists' => 'Validazione registrazione fallita: Email :email già esistente nel database',
        'registration_password_too_weak' => 'Validazione registrazione fallita: Password non soddisfa i requisiti di sicurezza',
        'registration_password_confirmation_mismatch' => 'Validazione registrazione fallita: Conferma password non corrispondente',
        'registration_invalid_email_format' => 'Validazione registrazione fallita: Formato email non valido: :email',
        'registration_required_field_missing' => 'Validazione registrazione fallita: Campi obbligatori mancanti: :fields',
        'registration_validation_comprehensive_failed' => 'Validazione registrazione fallita: Rilevati errori di validazione multipli',

        // Biography errors
        'biography_index_failed' => 'Errore nel recupero delle biografie utente. Verifica la query di paginazione e i filtri applicati.',
        'biography_validation_failed' => 'Validazione fallita per creazione/aggiornamento biografia. Controlla le regole di validazione Laravel.',
        'biography_create_failed' => 'Fallimento critico nella creazione biografia. Possibili cause: DB constraint violation, filesystem error.',
        'biography_access_denied' => 'Accesso negato a biografia. User ID non corrisponde a owner_id e biografia non è pubblica.',
        'biography_show_failed' => 'Errore nel caricamento dettagli biografia. Possibili cause: relazione mancante, eager loading fallito.',
        'biography_update_denied' => 'Tentativo di aggiornamento biografia senza ownership. User ID non corrisponde al proprietario.',
        'biography_update_failed' => 'Fallimento aggiornamento biografia. Possibili cause: DB lock, constraint violation.',
        'biography_type_change_invalid' => 'Tentativo cambio tipo biografia da "chapters" a "single" con capitoli esistenti.',
        'biography_delete_denied' => 'Tentativo eliminazione biografia senza ownership. User ID non corrisponde al proprietario.',
        'biography_delete_failed' => 'Fallimento eliminazione biografia. Possibili cause: DB constraint violation, cascade delete failure.',

        // Chapter errors
        'biography_chapter_validation_failed' => 'Validazione fallita per operazione capitolo biografia. Controlla date range validation.',
        'biography_chapter_create_failed' => 'Fallimento creazione capitolo biografia. Possibili cause: parent biography type mismatch.',
        'biography_chapter_access_denied' => 'Accesso negato a capitolo biografia. Verifica ownership via parent biography.',
        'biography_chapter_update_failed' => 'Fallimento aggiornamento capitolo biografia. Possibili cause: date constraint violation.',
        'biography_chapter_delete_failed' => 'Fallimento eliminazione capitolo biografia. Possibili cause: DB constraint.',
        'biography_chapter_reorder_failed' => 'Fallimento riordinamento capitoli biografia. Possibili cause: transaction rollback.',

        // Media errors
        'biography_media_upload_failed' => 'Fallimento upload media biografia. Possibili cause: Spatie media library error.',
        'biography_media_validation_failed' => 'Validazione media biografia fallita. Controlla file type validation, size limits.',
        'biography_media_delete_failed' => 'Fallimento eliminazione media biografia. Possibili cause: Spatie media library error.',

         // Missing Chapter Error Codes - Developer Messages
        'biography_chapter_index_failed' => 'Errore nel recupero dei capitoli biografia. Verifica la query di ordinamento e i filtri di pubblicazione. Context: biography_id, user_id, order_by.',
        'biography_chapter_show_failed' => 'Errore nel caricamento dettagli capitolo biografia. Possibili cause: relazione mancante, eager loading fallito, o corruzione dati capitolo. Context: biography_id, chapter_id, user_id.',

     ],
    'user' => [
        // == Existing Entries ==
        'authentication_error' => 'Non hai l\'autorizzazione per eseguire questa operazione.',
        'scan_error' => 'Non è stato possibile verificare la sicurezza del file in questo momento. Riprova più tardi.',
        'virus_found' => 'Il file ":fileName" contiene potenziali minacce ed è stato bloccato per la tua sicurezza.',
        'invalid_file_extension' => 'L\'estensione del file non è supportata. Le estensioni consentite sono: :allowed_extensions.',
        'max_file_size' => 'Il file è troppo grande. La dimensione massima consentita è :max_size.',
        'invalid_file_pdf' => 'Il PDF caricato non è valido o potrebbe essere danneggiato. Riprova.',
        'mime_type_not_allowed' => 'Il tipo di file che hai caricato non è supportato. I tipi consentiti sono: :allowed_types.',
        'invalid_image_structure' => 'L\'immagine che hai caricato non sembra valida. Prova con un\'altra immagine.',
        'invalid_file_name' => 'Il nome del file contiene caratteri non validi. Usa solo lettere, numeri, spazi, trattini e underscore.',
        'error_getting_presigned_url' => 'Si è verificato un problema temporaneo durante la preparazione del caricamento. Riprova.',
        'error_during_file_upload' => 'Si è verificato un errore durante il caricamento del file. Riprova o contatta l\'assistenza se il problema persiste.',
        'unable_to_save_bot_file' => 'Non è stato possibile salvare il file generato in questo momento. Riprova più tardi.',
        'unable_to_create_directory' => 'Errore interno del sistema durante la preparazione del salvataggio. Contatta l\'assistenza.',
        'unable_to_change_permissions' => 'Errore interno del sistema durante il salvataggio del file. Contatta l\'assistenza.',
        'impossible_save_file' => 'Non è stato possibile salvare il tuo file a causa di un errore di sistema. Riprova o contatta l\'assistenza.',
        'error_during_create_egi_record' => 'Si è verificato un errore durante il salvataggio delle informazioni. Il nostro team tecnico è stato informato.',
        'error_during_file_name_encryption' => 'Si è verificato un errore di sicurezza durante l\'elaborazione del file. Riprova.',
        'imagick_not_available' => 'Il sistema non è momentaneamente in grado di elaborare le immagini. Contatta l\'amministratore se il problema persiste.',
        'json_error' => 'Si è verificato un errore nell\'elaborazione dei dati. Controlla i dati inseriti o riprova più tardi. [Rif: JSON]',
        'generic_server_error' => 'Si è verificato un errore del server. Riprova più tardi o contatta l\'assistenza se il problema continua. [Rif: SERVER]',
        'file_not_found' => 'Il file richiesto non è stato trovato.',
        'unexpected_error' => 'Si è verificato un errore imprevisto. Il nostro team tecnico è stato informato. Riprova più tardi. [Rif: UNEXPECTED]',
        'error_deleting_local_temp_file' => 'Errore interno durante la pulizia dei file temporanei. Contatta l\'assistenza.', // Messaggio più generico per l'utente
        'acl_setting_error' => 'Non è stato possibile impostare i permessi corretti per il file. Riprova o contatta l\'assistenza.',
        'invalid_input' => 'Il valore fornito per :param non è valido. Controlla l\'input e riprova.',
        'temp_file_not_found' => 'Si è verificato un problema temporaneo con il file :file. Riprova.',
        'error_deleting_ext_temp_file' => 'Errore interno durante la pulizia dei file temporanei esterni. Contatta l\'assistenza.',
        'ucm_delete_failed' => 'Si è verificato un errore durante l\'eliminazione della configurazione. Riprova più tardi.',
        'undefined_error_code' => 'Si è verificato un errore imprevisto. Contatta il supporto se il problema persiste. [Rif: UNDEFINED]',
        'fallback_error' => 'Si è verificato un problema inatteso nel sistema. Riprova più tardi o contatta l\'assistenza. [Rif: FALLBACK]',
        'fatal_fallback_failure' => 'Si è verificato un errore critico nel sistema. Contatta immediatamente l\'assistenza. [Rif: FATAL]',
        'ucm_audit_not_found' => 'Non sono disponibili informazioni storiche per questo elemento.',
        'ucm_duplicate_key' => 'Questa impostazione di configurazione esiste già.',
        'ucm_create_failed' => 'Impossibile salvare la nuova impostazione di configurazione. Riprova.',
        'ucm_update_failed' => 'Impossibile aggiornare l\'impostazione di configurazione. Riprova.',
        'ucm_not_found' => 'L\'impostazione di configurazione richiesta non è stata trovata.',
        'invalid_file' => 'Il file fornito non è valido. Controlla il file e riprova.',
        'invalid_file_validation' => 'Controlla il file nel campo :field. La validazione non è riuscita.',
        'error_saving_file_metadata' => 'Si è verificato un errore salvando i dettagli del file. Riprova il caricamento.',
        'server_limits_restrictive' => 'La configurazione del server potrebbe impedire questa operazione. Contatta l\'assistenza se il problema persiste.',
        'generic_internal_error' => 'Si è verificato un errore interno. Il nostro team tecnico è stato informato e sta lavorando per risolverlo.', // Messaggio generico riutilizzabile
        'egi_auth_required' => 'Effettua il login per caricare un EGI.',
        'egi_file_input_error' => 'Seleziona un file valido da caricare.',
        'egi_validation_failed' => 'Correggi i campi evidenziati nel modulo.',
        'egi_collection_init_error' => 'Impossibile preparare la tua collection. Contatta il supporto se il problema persiste.',
        'egi_storage_failure' => 'Fallito il salvataggio sicuro del file EGI. Riprova o contatta il supporto.',
        'egi_unexpected_error' => 'Si è verificato un errore inaspettato durante l\'elaborazione del tuo EGI. Riprova più tardi.',
        'egi_unauthorized_access' => 'Accesso non autorizzato. Effettua il login.',
        'egi_page_rendering_error' => 'Si è verificato un problema durante il caricamento della pagina. Riprova più tardi o contatta l\'assistenza.',
        'egi_update_failed' => 'Impossibile aggiornare l\'EGI. Si prega di riprovare.',
        'egi_delete_failed' => 'Impossibile eliminare l\'EGI. Si prega di riprovare.',
        'invalid_egi_file' => 'Il file EGI non può essere elaborato a causa di errori di validazione. Verifica il formato e il contenuto del file.',
        'error_during_egi_processing' => 'Si è verificato un errore durante l\'elaborazione del file EGI. Il nostro team è stato avvisato e analizzerà il problema.',

        // Errori di creazione Wallet (messaggi per utenti)
        'wallet_creation_failed' => 'Si è verificato un problema durante la configurazione del wallet per questa collezione. Il nostro team è stato avvisato e risolverà questo problema.',
        'wallet_insufficient_quota' => 'Non hai quota royalty sufficiente per questa operazione. Modifica i valori di royalty e riprova.',
        'wallet_address_invalid' => 'L\'indirizzo del wallet fornito non è valido. Controlla il formato e riprova.',
        'wallet_not_found' => 'Il wallet richiesto non è stato trovato. Verifica le tue informazioni e riprova.',
        'wallet_already_exists' => 'Un wallet è già configurato per questa collezione. Utilizza il wallet esistente o contatta l\'assistenza per aiuto.',
        'wallet_invalid_secret' => 'La chiave segreta inserita non è corretta. Riprova.',
        'wallet_validation_failed' => 'Il formato dell\'indirizzo wallet non è valido. Controlla e riprova.',
        'wallet_connection_failed' => 'Impossibile connettere il tuo wallet in questo momento. Riprova più tardi.',
        'wallet_disconnect_failed' => 'Si è verificato un problema durante la disconnessione del wallet. Aggiorna la pagina.',

        // COLLECTION
        'collection_creation_failed' => 'Impossibile creare la tua collezione. Riprova più tardi o contatta il supporto.',
        'collection_find_create_failed' => 'Impossibile accedere alle tue collezioni. Riprova più tardi.',

        // == New Entries ==
        'authorization_error' => 'Non disponi dei permessi necessari per eseguire questa azione.',
        'csrf_token_mismatch' => 'La tua sessione è scaduta o non è valida. Per favore, ricarica la pagina e riprova.',
        'route_not_found' => 'La pagina o la risorsa che hai richiesto non è stata trovata.',
        'method_not_allowed' => 'L\'azione che hai tentato di eseguire non è permessa su questa risorsa.',
        'too_many_requests' => 'Stai eseguendo azioni troppo rapidamente. Attendi qualche istante e riprova.',
        'database_error' => 'Si è verificato un errore nel database. Riprova più tardi o contatta l\'assistenza. [Rif: DB]',
        'record_not_found' => 'L\'elemento che hai richiesto non è stato trovato.',
        'validation_error' => 'Per favore, correggi gli errori evidenziati nel modulo e riprova.', // Messaggio user generico
        'utm_load_failed' => 'Il sistema ha riscontrato un problema nel caricamento delle impostazioni della lingua. Alcune funzionalità potrebbero essere limitate.',
        'utm_invalid_locale' => 'L\'impostazione della lingua richiesta non è disponibile.',
        // Messaggi user per errori interni UEM (usare generic_internal_error)
        'uem_email_send_failed' => null,
        'uem_slack_send_failed' => null,
        'uem_recovery_action_failed' => null,
        'user_unauthenticated_access' => 'Autenticazione richiesta. Per favore, effettua il login per continuare.',
        'set_current_collection_forbidden' => 'Non hai i permessi necessari per accedere o impostare questa collezione come corrente.',
        'set_current_collection_failed' => 'Si è verificato un errore imprevisto durante l_aggiornamento delle tue preferenze. Il nostro team è stato notificato. Riprova più tardi.',
        'auth_required' => 'Devi essere connesso per eseguire questa azione.',
        'auth_required_for_like' => 'Devi essere connesso per mettere "mi piace" agli elementi.',
        'like_toggle_failed' => 'Ci dispiace, non siamo riusciti a elaborare la tua richiesta di "mi piace". Riprova.',

        // User messages for reservations system
        'reservation_egi_not_available' => 'Questo EGI non è attualmente disponibile per la prenotazione.',
        'reservation_amount_too_low' => 'L\'importo offerto è troppo basso. Inserisci un importo più alto.',
        'reservation_unauthorized' => 'Devi connettere il tuo wallet o accedere per effettuare una prenotazione.',
        'reservation_certificate_generation_failed' => 'Non siamo riusciti a generare il tuo certificato di prenotazione. Il nostro team è stato informato.',
        'reservation_certificate_not_found' => 'Il certificato richiesto non è stato trovato.',
        'reservation_already_exists' => 'Hai già una prenotazione attiva per questo EGI.',
        'reservation_cancel_failed' => 'Non siamo riusciti ad annullare la tua prenotazione. Riprova più tardi.',
        'reservation_unauthorized_cancel' => 'Non hai il permesso per annullare questa prenotazione.',
        'reservation_status_failed' => 'Impossibile recuperare lo stato della prenotazione. Riprova più tardi.',
        'reservation_unknown_error' => 'Qualcosa è andato storto con la tua prenotazione. Il nostro team è stato informato.',

        // User messages for statistics system
        'statistics_calculation_failed' => 'Impossibile caricare le tue statistiche al momento. Il nostro team è stato notificato. Riprova più tardi.',
        'icon_not_found' => 'Icona temporaneamente non disponibile. Uso icona predefinita.',
        'icon_retrieval_failed' => 'Icona temporaneamente non disponibile. Uso icona predefinita.',
        'statistics_cache_clear_failed' => 'Impossibile aggiornare cache statistiche. Riprova.',
        'statistics_summary_failed' => 'Impossibile caricare riassunto statistiche. Riprova.',

        // EGI Upload Handler Service-Based Architecture
        'egi_collection_service_error' => 'Si è verificato un errore durante la gestione della tua collezione. Il nostro team tecnico è stato avvisato.',
        'egi_wallet_service_error' => 'Errore durante la configurazione del tuo wallet per questa collezione. Riprova tra qualche minuto.',
        'egi_role_service_error' => 'Errore durante l\'assegnazione dei permessi di creator. Contatta il supporto se il problema persiste.',
        'egi_service_integration_error' => 'Si è verificato un errore interno del sistema. I nostri tecnici stanno già investigando.',
        'egi_enhanced_authentication_error' => 'La tua sessione non è valida. Effettua nuovamente l\'accesso al tuo Rinascimento.',
        'egi_file_input_validation_error' => 'Il file che hai caricato non è valido o è corrotto. Verifica il formato e riprova.',
        'egi_metadata_validation_error' => 'Alcuni dati inseriti non sono corretti. Controlla i campi evidenziati e riprova.',
        'egi_data_preparation_error' => 'Errore durante l\'elaborazione del tuo file. Verifica che sia un\'immagine valida.',
        'egi_record_creation_error' => 'Errore durante la creazione del tuo EGI. Il team tecnico è stato avvisato automaticamente.',
        'egi_file_storage_error' => 'Errore durante il salvataggio sicuro del tuo file. Riprova l\'upload.',
        'egi_cache_invalidation_error' => 'Il tuo EGI è stato caricato, ma potrebbe volerci qualche minuto prima che appaia ovunque.',

        // Collection Service Enhanced
        'collection_creation_enhanced_error' => 'Non siamo riusciti a creare la tua collezione. Riprova o contatta il supporto.',
        'collection_validation_error' => 'I dati della collezione non sono validi. Verifica e riprova.',
        'collection_limit_exceeded_error' => 'Hai raggiunto il limite massimo di collezioni. Contatta il supporto per aumentarlo.',
        'collection_wallet_attachment_failed' => 'La collezione è stata creata, ma con problemi nella configurazione wallet. Contatta il supporto.',
        'collection_role_assignment_failed' => 'La collezione è stata creata, ma con problemi nei permessi. Contatta il supporto.',
        'collection_ownership_mismatch_error' => 'Non hai i permessi per accedere a questa collezione.',
        'collection_current_update_error' => 'Errore durante l\'aggiornamento della tua collezione attiva. Riprova.',

        // Errori aggiornamento collezione corrente utente
        'user_current_collection_update_failed' => 'Abbiamo riscontrato un problema critico durante la configurazione della tua collezione. Il nostro team tecnico è stato avvisato e risolverà la questione immediatamente. Ti invitiamo a riprovare tra qualche momento o contattare il supporto se il problema persiste.',
        'user_current_collection_validation_failed' => 'Si è verificato un problema con la selezione della tua collezione. Assicurati di avere i permessi appropriati per questa collezione e riprova. Se continui ad avere difficoltà, contatta il nostro team di supporto.',

        // Enhanced Storage
        'egi_storage_disk_config_error' => 'Problema di configurazione del sistema di storage. Il team tecnico è stato avvisato.',
        'egi_storage_emergency_fallback_failed' => 'Errore critico del sistema di archiviazione. I tecnici stanno investigando.',
        'egi_temp_file_read_error' => 'Non riusciamo a leggere il file che hai caricato. Riprova con un file diverso.',

        // Enhanced Authentication & Session
        'egi_session_auth_invalid' => 'La tua sessione è scaduta. Riconnetti il tuo wallet per continuare.',
        'egi_wallet_auth_mismatch' => 'Il wallet connesso non corrisponde al tuo account. Verifica la connessione.',

        // Enhanced Registration Errors
        'enhanced_registration_failed' => 'Si è verificato un errore durante la configurazione del tuo account nel Rinascimento Digitale. Il nostro team è stato avvisato.',
        'registration_user_creation_failed' => 'Non siamo riusciti a creare il tuo account. Verifica i dati inseriti e riprova.',
        'registration_collection_creation_failed' => 'Il tuo account è stato creato, ma non siamo riusciti a preparare la tua collezione. Contatta il supporto.',
        'registration_wallet_setup_failed' => 'La registrazione è quasi completa, ma ci sono problemi con la configurazione del wallet. Il supporto ti contatterà presto.',
        'registration_role_assignment_failed' => 'La registrazione è quasi completa, ma ci sono problemi con i permessi del tuo account. Il supporto ti aiuterà.',
        'registration_gdpr_consent_failed' => 'Errore durante il salvataggio delle tue preferenze privacy. Riprova o contatta il supporto.',
        'registration_ecosystem_setup_incomplete' => 'La registrazione non è stata completata completamente. Il nostro team sta verificando e ti contatterà.',
        'registration_validation_enhanced_failed' => 'Alcuni dati inseriti non sono corretti. Controlla i campi evidenziati e riprova.',
        'registration_user_type_invalid' => 'Il ruolo selezionato non è valido. Scegli tra Creator, Mecenate, Acquirente o Azienda.',
        'registration_rate_limit_exceeded' => 'Troppe richieste di registrazione. Riprova tra qualche minuto.',
        'registration_page_load_error' => 'Errore nel caricamento della pagina di registrazione. Ricarica la pagina.',
        'permission_based_registration_failed_user' => 'Si è verificato un errore durante la registrazione. Ti preghiamo di riprovare o contattare il supporto se il problema persiste.',
        'algorand_wallet_generation_failed_user' => 'Errore nella creazione del wallet digitale. Ti preghiamo di riprovare la registrazione.',
        'ecosystem_setup_failed_user' => 'La registrazione è stata completata, ma si è verificato un errore nella configurazione iniziale. Puoi completare la configurazione dal tuo profilo.',
        'user_domain_initialization_failed_user' => 'Registrazione completata con successo! Alcune sezioni del profilo potrebbero richiedere configurazione aggiuntiva.',
        'gdpr_consent_processing_failed_user' => 'Errore nell\'elaborazione dei consensi privacy. Ti preghiamo di verificare le tue scelte e riprovare.',
        'role_assignment_failed_user' => 'Errore nella configurazione del tipo account. Ti preghiamo di contattare il supporto.',
        'personal_data_view_failed' => 'Si è verificato un errore nel caricamento dei tuoi dati personali. Per favore riprova tra qualche minuto o contatta il supporto se il problema persiste.',
        'personal_data_update_failed' => 'Non è stato possibile salvare le modifiche ai tuoi dati personali. Verifica che tutti i campi siano compilati correttamente e riprova.',
        'personal_data_export_failed' => 'Si è verificato un errore durante l\'esportazione dei tuoi dati. Riprova più tardi o contatta il supporto per assistenza.',
        'personal_data_deletion_failed' => 'Non è stato possibile elaborare la richiesta di cancellazione dei tuoi dati. Ti preghiamo di contattare il nostro supporto per ricevere assistenza immediata.',
        'gdpr_export_rate_limit' => 'Puoi richiedere un\'esportazione dei tuoi dati una volta ogni 30 giorni. La prossima esportazione sarà disponibile tra qualche giorno.',
        'gdpr_violation_attempt' => 'Non puoi aggiornare i tuoi dati personali senza aver fornito il consenso appropriato. Accetta i termini di elaborazione dei dati per continuare.',
        'gdpr_notification_send_failed_user' => 'Spiacenti, si è verificato un problema tecnico e non è stato possibile inviare una notifica importante. Il nostro team è stato avvisato.',
        'gdpr_notification_dispatch_failed' => 'Si è verificato un problema durante l\'elaborazione della tua richiesta relativa alla privacy. Il nostro team è stato informato e risolverà il problema al più presto.',
        'gdpr_notification_persistence_failed' => 'Non è stato possibile completare l\'operazione richiesta per la gestione dei tuoi dati. Ti invitiamo a riprovare più tardi o contattare il supporto.',
        'gdpr_service_unavailable' => 'Il servizio di gestione consensi non è al momento disponibile. Ti preghiamo di riprovare più tardi.',

        'legal_content_load_failed' => 'Impossibile caricare i documenti legali in questo momento. Riprova più tardi.',
        // Nota: per TERMS_ACCEPTANCE_CHECK_FAILED usiamo la traduzione 'generic_error' già esistente

        // Registration validation errors - Messaggi user-friendly
        'registration_email_already_exists' => 'Questo indirizzo email è già registrato. Prova a effettuare il login.',
        'registration_password_too_weak' => 'La password deve essere lunga almeno 8 caratteri e includere lettere e numeri.',
        'registration_password_confirmation_mismatch' => 'La conferma della password non corrisponde. Riprova.',
        'registration_invalid_email_format' => 'Inserisci un indirizzo email valido.',
        'registration_required_field_missing' => 'Compila tutti i campi obbligatori.',
        'registration_validation_comprehensive_failed' => 'Controlla il modulo e correggi gli errori.',

        // Biography errors - User-friendly messages
        'biography_index_failed' => 'Non è stato possibile caricare le tue biografie. Riprova tra qualche momento.',
        'biography_validation_failed' => 'Alcuni campi non sono stati compilati correttamente. Controlla i dati inseriti.',
        'biography_create_failed' => 'Non è stato possibile creare la biografia. Riprova tra qualche momento.',
        'biography_access_denied' => 'Non hai i permessi per visualizzare questa biografia.',
        'biography_show_failed' => 'Non è stato possibile caricare i dettagli della biografia. Riprova tra qualche momento.',
        'biography_update_denied' => 'Non hai i permessi per modificare questa biografia.',
        'biography_update_failed' => 'Non è stato possibile salvare le modifiche alla biografia.',
        'biography_type_change_invalid' => 'Impossibile cambiare il tipo di biografia perché sono presenti dei capitoli. Elimina prima tutti i capitoli.',
        'biography_delete_denied' => 'Non hai i permessi per eliminare questa biografia.',
        'biography_delete_failed' => 'Non è stato possibile eliminare la biografia. Riprova tra qualche momento.',

        // Chapter errors
        'biography_chapter_validation_failed' => 'Alcuni campi del capitolo non sono stati compilati correttamente.',
        'biography_chapter_create_failed' => 'Non è stato possibile creare il capitolo. Controlla che la biografia sia impostata per "capitoli".',
        'biography_chapter_access_denied' => 'Non hai i permessi per visualizzare questo capitolo.',
        'biography_chapter_update_failed' => 'Non è stato possibile salvare le modifiche al capitolo.',
        'biography_chapter_delete_failed' => 'Non è stato possibile eliminare il capitolo.',
        'biography_chapter_reorder_failed' => 'Non è stato possibile riordinare i capitoli. Riprova o aggiorna la pagina.',

        // Media errors
        'biography_media_upload_failed' => 'Non è stato possibile caricare l\'immagine. Controlla che sia un\'immagine valida (JPG, PNG) sotto i 5MB.',
        'biography_media_validation_failed' => 'Il file selezionato non è valido. Usa immagini JPG, PNG o WebP fino a 5MB.',
        'biography_media_delete_failed' => 'Non è stato possibile eliminare l\'immagine. Riprova tra qualche momento.',

        // Missing Chapter Error Codes - User Messages
        'biography_chapter_index_failed' => 'Non è stato possibile caricare i capitoli della biografia. Riprova tra qualche momento o aggiorna la pagina.',
        'biography_chapter_show_failed' => 'Non è stato possibile caricare i dettagli del capitolo. Riprova tra qualche momento.',
    ],

    // Generic message (used by UserInterfaceHandler if no specific message found)
    'generic_error' => 'Si è verificato un errore. Riprova più tardi o contatta l\'assistenza.',
];
