#!/bin/bash

if [ "$1" == "" ]; then
  echo "Please specify the new version of the extension"
  exit 1
fi

zip -r botamp_botamp-$1.zip . -x './.*'
