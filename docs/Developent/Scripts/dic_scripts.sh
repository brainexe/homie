#!/usr/bin/env bash

grep -oP "this->get\('\K(.*?)'"  cache/dic.php | sort | uniq -c | sort -n
