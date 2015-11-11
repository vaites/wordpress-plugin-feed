#!/bin/bash

VERSION=`cat composer.json | grep 'version' | awk '{print $2}' \
       | sed -e 's/"//g' | sed -e 's/,//'`

BUILD="./build/wordpress-plugin-feed-$VERSION.tar.gz"

if [ ! -f $BUILD ]; then
    composer update

    tar czvf $BUILD ./ --exclude=.env --exclude=.git --exclude=.idea --exclude=nbproject \
                       --exclude=reports --exclude=build* --exclude=cache/zfcache-* --exclude=composer.lock
else
    echo "$BUILD already exists..."
fi