#!/bin/sh

set -eux

#  If CODESPACE_NAME is set, set SITE_HOST.
if [ -n ${CODESPACE_NAME+x} ]; then
	SITE_HOST="https://${CODESPACE_NAME}-8888.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
	echo "{\"config\": {\"WP_SITEURL\": \"${SITE_HOST}\",\"WP_HOME\": \"${SITE_HOST}\"}}" | jq . > /workspaces/omniform/.wp-env.override.json
fi

# Install PHP Scoper globally
composer global require humbug/php-scoper

# Install dependencies
cd /workspaces/omniform

composer install --no-autoloader --no-dev --prefer-dist
php-scoper add-prefix --force
rm -rf ./vendor

composer install

npm install && npm run build