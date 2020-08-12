#!/bin/sh
mkdir tmp && \
mv composer.json tmp/ && \
composer require --dev techpivot/phalcon-ci-installer && \
vendor/bin/install-phalcon.sh 4.0.x && \
rm composer.lock && \
rm composer.json && \
mv tmp/composer.json ./ && \
rmdir tmp
