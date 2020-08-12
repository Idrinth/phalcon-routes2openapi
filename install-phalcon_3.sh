#!/bin/sh
mkdir tmp && \
mv composer.json tmp/ && \
composer init --name tmp/tmp && \
composer require --dev techpivot/phalcon-ci-installer && \
vendor/bin/install-phalcon.sh 3.2.x && \
rm composer.lock && \
rm composer.json && \
mv tmp/composer.json ./ && \
rmdir tmp
