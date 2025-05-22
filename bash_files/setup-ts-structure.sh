#!/bin/bash

# Vai nella directory resources/ts del tuo progetto
mkdir -p resources/ts

# Funzione per creare directory e file
create_file() {
  local path=$1
  local file=$2
  mkdir -p "$path"
  touch "$path/$file"
  echo "// $file" > "$path/$file"
}

# config
create_file resources/ts/config appConfig.ts

# dom
create_file resources/ts/dom domElements.ts

# enums (opzionale, ma lo creiamo vuoto)
create_file resources/ts/enums index.ts

# features/auth
create_file resources/ts/features/auth authService.ts
create_file resources/ts/features/auth walletConnect.ts

# features/collections
create_file resources/ts/features/collections collectionService.ts
create_file resources/ts/features/collections collectionUI.ts

# features/mobile
create_file resources/ts/features/mobile mobileMenu.ts

# services
create_file resources/ts/services uemClientService.ts
create_file resources/ts/services i18nService.ts

# ui
create_file resources/ts/ui navbarManager.ts
create_file resources/ts/ui modalManagerService.ts

# utils
create_file resources/ts/utils csrf.ts
create_file resources/ts/utils helpers.ts

# vendor/ultra/uem
mkdir -p resources/ts/vendor/ultra/uem
touch resources/ts/vendor/ultra/uem/index.ts
echo "// index.ts for UEM client module" > resources/ts/vendor/ultra/uem/index.ts

# main file e global definitions
touch resources/ts/main.ts
echo "// Entry point of the application" > resources/ts/main.ts

touch resources/ts/global.d.ts
echo "// Global type definitions" > resources/ts/global.d.ts

# open-close-modal.ts
touch resources/ts/open-close-modal.ts
echo "// Specific modal logic for #upload-modal" > resources/ts/open-close-modal.ts

echo "✅ Struttura creata con successo!"
