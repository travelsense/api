#!/usr/bin/env bash
#
# Usage: bin/build-tag.sh <TAG>
#
set -e

TIMESTAMP=`date +%Y%m%d%H%M%S`
WORKSPACE="/tmp/${TIMESTAMP}"
TAG=$1
git fetch origin
git fetch --tags
[[ -z ${TAG} ]] && TAG=`git tag -l --sort "v:refname" | tail -n 1`
[[ -z `git tag -l ${TAG}` ]] && echo 'Tag not found' && exit 1
echo "Building tag ${TAG}"
rm -rf ${WORKSPACE} && mkdir ${WORKSPACE}
git archive --format=tar ${TAG} | tar -x -C ${WORKSPACE}
pushd ${WORKSPACE} > /dev/null
composer install --no-dev
echo ${TAG} > VERSION
printf "${TAG} ${USER}@${HOSTNAME} `date`\n\n`uname -a`\n\n`php -v`\n\n`composer -V`" > RELEASE
tar -zcf ${TAG}.tar.gz * --remove-files
popd > /dev/null
mkdir -p build
mv ${WORKSPACE}/${TAG}.tar.gz build/
printf "\nBuilt: ${TAG}\n\n"
