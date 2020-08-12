#!/bin/sh

mkdir tmp && \
mv composer.json tmp/ && \

git clone git://github.com/phalcon/php-zephir-parser.git && \
cd php-zephir-parser && \
phpize && \
./configure && \
make && \
make install && \
echo "extension = zephir_parser.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini &&\
cd .. && \

composer init --name tmp/tmp && \
composer config repositories.phalcon-zephir-update '{"type":"vcs","url":"https://github.com/idrinth/phalcon-ci-installer","no-api":true}' && \
composer require --dev techpivot/phalcon-ci-installer:dev-patch-1 && \
vendor/bin/install-phalcon.sh 4.0.x && \

rm composer.lock && \
rm composer.json && \
mv tmp/composer.json ./ && \
rmdir tmp && \
rm -r php-zephir-parser
