#!/bin/bash

echo "🔧 Forzatura uso di Git per Composer (evita API GitHub)..."

# Imposta i protocolli su git
composer config --global github-protocols git

# Scrive config.json per renderlo permanente
mkdir -p ~/.config/composer
cat > ~/.config/composer/config.json <<EOF
{
  "config": {
    "github-protocols": ["git"]
  }
}
EOF

# Elimina auth.json se presente (per evitare ambiguità)
rm -f ~/.config/composer/auth.json

# Pulisce cache Composer
composer clear-cache

echo "✅ Configurazione completata. Composer userà solo git clone."
