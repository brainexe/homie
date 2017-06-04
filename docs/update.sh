#!/usr/bin/env bash

# This script will deploy the current version of "homie" to the local directory

git fetch --all
git reset --hard origin/master

wget https://jenkins.mdoetsch.de/job/BuildHomie-Web/lastSuccessfulBuild/artifact/*zip*/archive.zip -O archive.zip

(rm -rf web/* && unzip -q archive.zip && cp -rf archive/* . && rm -rf archive archive.zip && echo "updated /web/ directory")

(rm -rf cache/dic* && composer install --prefer-dist --no-dev -o && php console cc && echo "updated composer + backend caches")

(npm set progress=false && NODE_ENV=production npm install --production && echo "updated nodejs")

echo "done"
