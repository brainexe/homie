#!/usr/bin/env bash

# This script will deploy the current version of "homie" to the local directory

#git fetch --all
#git reset --hard origin/master

wget https://jenkins.mdoetsch.de/job/BuildHomie-Web/lastSuccessfulBuild/artifact/*zip*/archive.zip -O archive.zip

(rm -Rf web/* && unzip -q archive.zip && cp -Rf archive/* . && rm archive archive.zip -Rf && echo "updated /web/ directory")

(composer install --prefer-dist --no-dev -o && php console cc && echo "updated composer + backend caches")

(cd nodejs && npm install --production && echo "updated nodejs")

echo "done"
