#!/usr/bin/env bash

DEVICE=$(ls /dev/ttyUSB* /dev/ttyACM*)

cd node_modules/duino/src/du
arduino du.ino --upload --port $DEVICE
