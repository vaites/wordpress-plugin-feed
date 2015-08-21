#!/bin/bash

VERSION=`cat composer.json | grep 'version' | awk '{print $2}' \
       | sed -e 's/"//g' | sed -e 's/,//'`

tar czvf "./build/wordpress-plugin-feed-$VERSION.tar.gz" ./ \
    --exclude=.env --exclude=.git --exclude=.idea --exclude=nbproject \
    --exclude=build* --exclude=cache/zfcache-* --exclude=composer.lock