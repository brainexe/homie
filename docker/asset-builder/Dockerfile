FROM node:alpine

WORKDIR /www/homie

COPY package.json /www/homie/package.json

RUN apk --update add git gettext \
  && npm install -q \
  && rm package.json \
  && apk del git

ENTRYPOINT cd /www/homie/ && npm run-script grunt buildAll
