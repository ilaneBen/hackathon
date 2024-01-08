#!/bin/bash

# Composer audit
composer_audit=$(composer audit --locked --format=json)
if [ -z "$composer_audit" ]; then
    echo '{"result": "Aucune faille"}' > Result/composer_audit_result.json
else
    echo "$composer_audit" > Result/composer_audit_result.json
fi

# Vérification de la version de PHP
php_version=$(php --version)
if [ -z "$php_version" ]; then
    echo '{"result": "Aucune version de PHP trouvée"}' > Result/php_version.json
else
    echo "{\"php_version\": \"$php_version\"}" > Result/php_version.json
fi

# Analyse statique avec PHPStan et configuration de la mémoire
phpstan_analysis=$(php -d memory_limit=512M vendor/bin/phpstan analyse -c phpstan.neon.dist --error-format=json)
if [ -z "$phpstan_analysis" ]; then
    echo '{"result": "Aucune erreur détectée par PHPStan"}' > Result/phpstan_analysis_result.json
else
    echo "$phpstan_analysis" > Result/phpstan_analysis_result.json
fi

# PHP Mess Detector (PHPMD) avec exclusion des répertoires d'entités
phpmd_result=$(./vendor/bin/phpmd src json phpmd.xml --exclude '*/src/Entity/*')
if [ -z "$phpmd_result" ]; then
    echo '{"result": "Aucune violation détectée par PHPMD"}' > Result/phpmd_result.json
else
    echo "$phpmd_result" > Result/phpmd_result.json
fi
