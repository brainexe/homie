#!/usr/bin/env bash

grep -ohP "uib-\K([\w]*)" assets/ -R | sort | uniq -c | sort -n -r
