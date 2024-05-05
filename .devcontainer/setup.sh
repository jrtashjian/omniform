#!/bin/sh

set -eux

if [ -z ${CODESPACE_NAME+x} ]; then
	SITE_HOST="http://localhost:8080"
else
	SITE_HOST="https://${CODESPACE_NAME}-8080.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
fi

echo "PATH=\"\$PATH:${HOME}/.composer/vendor/bin\"" >> $HOME/.bashrc
export PATH="$PATH:$HOME/.composer/vendor/bin"

cd /workspaces/omniform

# Prefix required Composer packages.
composer install --no-autoloader --no-dev --prefer-dist
php-scoper add-prefix --force

# Reset and install Composer dependencies.
rm -rf ./vendor
composer install
npm install && npm run build

# Install WordPress.
cd /var/www/html
echo "Setting up WordPress at $SITE_HOST"

wp core install --url="$SITE_HOST" \
    --title="OmniForm Development" \
    --admin_user="admin" --admin_email="admin@example.com" --admin_password="password" --skip-email
