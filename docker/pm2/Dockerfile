FROM node:alpine

WORKDIR /www/homie

COPY package.json /www/homie/package.json

RUN apk --update add git \
  && npm install -q --production \
  && rm package.json \
  && apk del git

VOLUME /www/homie/node_modules

CMD node ./nodejs/pm2_start.js && sleep 100000000
