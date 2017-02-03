#!/usr/bin/env bash

egrep -i '\.[a-z]{3,10}\("[^"]*",function\(a' web/*app*.js --color
