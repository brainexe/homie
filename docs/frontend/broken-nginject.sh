#!/usr/bin/env bash

egrep '[a-z]{3,}\("[a-zA-Z_\.]+",function\(a' web/*app*.js --color
