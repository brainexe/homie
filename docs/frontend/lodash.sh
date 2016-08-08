#!/usr/bin/env bash

grep -hoP "lodash\\.\\K(\\w*)" assets/js -r | sort | uniq -c | sort -nr
