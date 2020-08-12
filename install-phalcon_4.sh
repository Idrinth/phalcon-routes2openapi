#!/bin/sh
mkdir tmp && \
mv composer.json tmp/ && \
composer config repositories.phalcon-zephir-update '{"type":"vcs","url":"https://github.com/idrinth/phalcon-ci-installer","no-api":true}' && \
composer require --dev techpivot/phalcon-ci-installer:dev-patch-1 && \
vendor/bin/install-phalcon.sh 4.0.x && \
rm composer.lock && \
rm composer.json && \
mv tmp/composer.json ./ && \
rmdir tmp
