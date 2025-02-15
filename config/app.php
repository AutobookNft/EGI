<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hosting Services Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to define multiple hosting services
    | that your application may use. Each service is defined with its name and
    | a flag indicating whether it is the default hosting provider. This setting
    | is useful for dynamically referencing the appropriate hosting service in
    | notifications or alerts, allowing you to easily scale the application
    | when switching between different hosting services without modifying the
    | application code.
    |
    |--------------------------------------------------------------------------
    */
    'hosting_services' => [
        ['name' => 'Digital Ocean', 'is_default' => true],
        ['name' => 'AWS', 'is_default' => false],
        ['name' => 'Google Cloud', 'is_default' => false],
        ['name' => 'IPFS', 'is_default' => false],
    ],


    /*
    |--------------------------------------------------------------------------
    | EGI Asset
    |--------------------------------------------------------------------------
    |
    | Questo parametro determina se gli EGI asset sono abilitati o meno.
    |
    */
    'egi_asset' => env('EGI_ASSET', false),


    /*
    |--------------------------------------------------------------------------
    | PLATFORM_BLOCKCHAIN
    |--------------------------------------------------------------------------
    |
    | Questo parametro determina SU QUALE BLOCKCHAIN è basata la piattaforma.
    |
    */
    'platform_blockchain' => env('PLATFORM_BLOCKCHAIN', 'algorand'),

    /*
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
     */
    'natan' => env('NATAN'),
    'natan_webp' => env('NATAN_WEBP'),
    'natan_png' => env('NATAN_PNG'),
    'natan_tr' => env('NATAN_TR'),
    'natan_01' => env('NATAN_01'),
    'logo_tr' => env('LOGO_TR'),
    'logo_01' => env('LOGO_01'),
    'logo_02' => env('LOGO_02'),
    'logo_03' => env('LOGO_03'),
    'logo_04' => env('LOGO_04'),
    'favicon' => env('FAVICON'),
    'default_cover' => env('DEFAULT_COVER'),
    'welcome_background' => env('WELCOME_BACKGROUD'),

    'platform_slogan' => env('PLATFORM_SLOGAN', 'pazzia'),

     /*
    |--------------------------------------------------------------------------
    | Royalty di default per Frangette
    |--------------------------------------------------------------------------
    |
     */
    'natan_wallet_address' => env('NATAN_WALLET_ADDRESS'),
    'natan_royalty_mint' => env('NATAN_ROYALTY_MINT'),
    'natan_royalty_rebind' => env('NATAN_ROYALTY_REBIND'),
    'epp_wallet_address' => env('EPP_WALLET_ADDRESS'),
    'epp_royalty_mint' => env('EPP_ROYALTY_BIND'),
    'epp_royalty_rebind' => env('EPP_ROYALTY_REBIND'),
    'mediator_royalty_mint' => env('MEDIATOR_ROYALTY_MINT'),
    'mediator_royalty_rebind' => env('MEDIATOR_ROYALTY_REBIND'),
    'creator_royalty_mint' => env('CREATOR_ROYALTY_MINT'),
    'creator_royalty_rebind' => env('CREATOR_ROYALTY_REBIND'),


    /*
    |--------------------------------------------------------------------------
    | Threshold royalty for Creator
    |--------------------------------------------------------------------------
    */
    'creator_royalty_mint_threshold'=> env('CREATOR_ROYALTY_MINT_THRESHOLD'),
    'creator_royalty_rebind_threshold'=> env('CREATOR_ROYALTY_REBIND_THRESHOLD'),


    /*
    |--------------------------------------------------------------------------
    | Bucket Path File Folder
    |--------------------------------------------------------------------------
    |
     */
    'bucket_folder_temp' => env('BUCKET_FOLDER_TEMP'),
    'bucket_temp_file_folder' => env('BUCKET_TMP_FILE_FOLDER'),
    'local_server' => env('LOCAL_SERVER'),
    'bucket_path_file_folder' => env('BUCKET_PATH_FILE_FOLDER'),
    'bucket_root_file_folder' => env('BUCKET_ROOT_FILE_FOLDER'),
    'bucket_root_utilities_files' => env('FOLDER_ROOT_UTILITY_FILES'),
    'bucket_uri_end_utilities_files' => env('FOLDER_URI_END_UTILITY_FILES'),
    'bucket_path_file_folder_metadata' => env('BUCKET_ROOT_FILE_FOLDER_METADATA'),
    'bucket_path_file_folder_read' => env('BUCKET_PATH_FILE_FOLDER_READ'),
    'do_access_key_id' => env('DO_ACCESS_KEY_ID'),
    'do_secret_access_key' => env('DO_SECRET_ACCESS_KEY'),
    'do_default_region' => env('DO_DEFAULT_REGION'),
    'do_bucket' => env('DO_BUCKET'),
    'do_use_path_style_endpoint' => env('DO_USE_PATH_STYLE_ENDPOINT'),
    'do_endpoint' => env('DO_ENDPOINT'),
    'do_bucket_folder' => env('DO_BUCKET_FOLDER'),

    /*
    |--------------------------------------------------------------------------
    | Wallet balance per la simulazione del minting e rebinding
    |--------------------------------------------------------------------------
    |
    */
    'virtual_wallet_balance' => 1000000,

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | L'id dell'utente di sistema
    |--------------------------------------------------------------------------
     */
    'natan_id' => env('NATAN_ID', 1),

    /*
    |--------------------------------------------------------------------------
    | ID predefinito dell'utente EPP
    |--------------------------------------------------------------------------
     */
    'epp_id' => env('EPP_ID', 2),

    /*
    |--------------------------------------------------------------------------
    | Application build version
    |--------------------------------------------------------------------------
     */
    'version' => env('APP_VERSION'),

    /*
    |--------------------------------------------------------------------------
    | EMAIL DI SERVIZIO
    |--------------------------------------------------------------------------
    |
     */
    'errors_email' => env('MAIL_ERRORS'),

    /*
    |--------------------------------------------------------------------------
    | Chiave di crittografia
    |--------------------------------------------------------------------------
    |
     */
    'data_crypto_key' => env('DATA_CRYPTO_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),
    'languages' => [
        'it' => 'Italiano',
        'en' => 'English',
        'es' => 'Español',
        'pt' => 'Português',
        'fr' => 'Français',
        'de' => 'Deutsch'
    ],

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
