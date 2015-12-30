#!/usr/bin/env bash

# This script will deploy the current version of "homie" to the local directory

wget https://jenkins.mdoetsch.de/job/BuildHomie/lastSuccessfulBuild/artifact/*zip*/archive.zip -O archive.zip
unzip -q archive.zip

#todo touch all
#find vendor/* src/* web/* -mmin +5 -exec rm {} \;
cp -Rf archive/* .
mkdir -p cache
php console cc

cd nodejs
npm install --production
cd -

rm archive.zip archive -Rf

