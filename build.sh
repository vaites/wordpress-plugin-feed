#!/bin/bash

VERSION=`git tag --sort version:refname | tail -n 1 | sed -e 's/v//'`

BUILD="./build/wordpress-plugin-feed-$VERSION.tar.gz"

if [ ! -f $BUILD ]; then
    composer update

    tar czvf $BUILD ./ --exclude=.env --exclude=.git --exclude=.idea --exclude=nbproject \
                       --exclude=reports --exclude=build* --exclude=cache/zfcache-* --exclude=composer.lock
else
    echo "$BUILD already exists..."
fi