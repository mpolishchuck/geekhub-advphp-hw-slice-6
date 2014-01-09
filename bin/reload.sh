#!/bin/sh

cd `dirname "$0"`
cd ..

set -x

php app/console doctrine:database:drop --force

find app/cache/ -type d -maxdepth 1 -mindepth 1 | while read L; do
    rm -rf "$L"
done

rm -rf web/bundles
rm -rf web/css
rm -rf web/fonts
rm -rf web/images
rm -rf web/js

php app/console doctrine:database:create
php app/console doctrine:schema:create

php app/console braincrafted:bootstrap:install
php app/console assets:install --symlink
php app/console assetic:dump

php app/console cache:clear
php app/console doctrine:fixtures:load --no-interaction
