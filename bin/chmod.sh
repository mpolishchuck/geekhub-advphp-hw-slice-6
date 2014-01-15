#!/bin/sh

cd `dirname "$0"`
cd ..

set -x

chmod 0777 app/cache
chmod 0777 app/logs

find app/logs/ -name '*.log' -exec chmod 0666 {} \;

find app/cache/ -type d -maxdepth 1 -mindepth 1 | while read L; do
    chmod 0777 "$L"
    find "$L" -type d -exec chmod 0777 {} \;
    find "$L" -type f -exec chmod 0666 {} \;
done
