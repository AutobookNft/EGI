#!/bin/bash

echo "🚀 Creating standard Laravel package structure in the current directory..."

# --- Top Level Directories ---
echo "-> Creating top-level directories..."
mkdir -p config
mkdir -p database/factories
mkdir -p database/migrations
mkdir -p database/seeders
mkdir -p resources/assets/js
mkdir -p resources/assets/css
mkdir -p resources/lang/en
mkdir -p resources/views
mkdir -p routes
mkdir -p src
mkdir -p tests/Feature
mkdir -p tests/Unit

# --- src Subdirectories (Comprehensive) ---
echo "-> Creating src subdirectories..."
mkdir -p src/Console/Commands
mkdir -p src/Contracts # Or Interfaces
mkdir -p src/Enums
mkdir -p src/Events
mkdir -p src/Exceptions
mkdir -p src/Facades
mkdir -p src/Handlers # Specific for your use case
mkdir -p src/Http/Controllers
mkdir -p src/Http/Middleware
mkdir -p src/Http/Requests
mkdir -p src/Jobs
mkdir -p src/Listeners
mkdir -p src/Mail
mkdir -p src/Models
mkdir -p src/Notifications
mkdir -p src/Observers # If using model observers
mkdir -p src/Policies # For authorization
mkdir -p src/Providers
mkdir -p src/Rules # For custom validation rules
mkdir -p src/Services
mkdir -p src/Support # Or Utils / Helpers for utility classes
mkdir -p src/Traits
mkdir -p src/View/Components # For Blade components

# --- Placeholder Files (.gitkeep) ---
# Ensures Git tracks empty directories if needed
echo "-> Creating .gitkeep placeholders..."
touch config/.gitkeep
touch database/factories/.gitkeep
touch database/migrations/.gitkeep
touch database/seeders/.gitkeep
touch resources/assets/js/.gitkeep
touch resources/assets/css/.gitkeep
touch resources/lang/en/.gitkeep
touch resources/views/.gitkeep
touch routes/.gitkeep
touch src/Console/Commands/.gitkeep
touch src/Contracts/.gitkeep
touch src/Enums/.gitkeep
touch src/Events/.gitkeep
touch src/Exceptions/.gitkeep
touch src/Facades/.gitkeep
touch src/Handlers/.gitkeep
touch src/Http/Controllers/.gitkeep
touch src/Http/Middleware/.gitkeep
touch src/Http/Requests/.gitkeep
touch src/Jobs/.gitkeep
touch src/Listeners/.gitkeep
touch src/Mail/.gitkeep
touch src/Models/.gitkeep
touch src/Notifications/.gitkeep
touch src/Observers/.gitkeep
touch src/Policies/.gitkeep
# Keep Providers directory potentially empty for now
touch src/Rules/.gitkeep
touch src/Services/.gitkeep
touch src/Support/.gitkeep
touch src/Traits/.gitkeep
touch src/View/Components/.gitkeep
touch tests/Feature/.gitkeep
touch tests/Unit/.gitkeep

# --- Placeholder Root Files ---
echo "-> Creating placeholder root files..."
touch config/package-config.php # Example config file name
touch routes/web.php
touch routes/api.php
touch routes/console.php
touch src/Providers/PackageServiceProvider.php # Example Service Provider
touch tests/TestCase.php
touch tests/Pest.php # If using Pest
touch .gitignore
touch CHANGELOG.md
touch LICENSE.md
touch phpunit.xml.dist
touch README.md

# --- Minimal .gitignore Content ---
echo "-> Creating basic .gitignore..."
cat << EOF > .gitignore
/vendor/
*.log
.env
.phpunit.result.cache
EOF

# --- Minimal phpunit.xml.dist Content ---
# (Adapt paths if your tests dir structure changes)
echo "-> Creating basic phpunit.xml.dist..."
cat << EOF > phpunit.xml.dist
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./src</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <!-- <env name="DB_CONNECTION" value="sqlite"/> -->
        <!-- <env name="DB_DATABASE" value=":memory:"/> -->
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
EOF

# --- Minimal README.md Content ---
echo "-> Creating basic README.md..."
cat << EOF > README.md
# Package Name (e.g., Ultra EGI Module)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/your-vendor/your-package.svg?style=flat-square)](https://packagist.org/packages/your-vendor/your-package)
[![Total Downloads](https://img.shields.io/packagist/dt/your-vendor/your-package.svg?style=flat-square)](https://packagist.org/packages/your-vendor/your-package)

Description of your package.

## Installation

You can install the package via composer:

\`\`\`bash
composer require your-vendor/your-package
\`\`\`

## Usage

\`\`\`php
// Usage examples
\`\`\`

## Testing

\`\`\`bash
composer test
\`\`\`

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see CONTRIBUTING.md for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Fabio Cherici](https://github.com/your-github)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
EOF

echo "✅ Package structure created successfully!"
echo "👉 Next steps:"
echo "   1. Update 'README.md', 'LICENSE.md', 'CHANGELOG.md'."
echo "   2. Fill in your actual 'composer.json' details."
echo "   3. Implement your 'PackageServiceProvider.php'."
echo "   4. Start coding in the 'src/' directory!"
echo "   5. Remove any directories you don't need."
