#!/usr/bin/env bash

DEVICE=$(ls /dev/ttyUSB* /dev/ttyACM*)
TYPE="du"

cd node_modules/duino/src/
arduino $TYPE.ino --upload --port $DEVICE
