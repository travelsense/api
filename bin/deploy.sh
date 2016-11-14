#!/usr/bin/env bash
#
# Usage: bin/deploy.sh [-s] [-i] [-t <tag>]
#

# Configuration
RELEASE_DIR=/www/release
CURRENT=/www/current

while getopts ":sit:" OPT; do
  case ${OPT} in
    t)
      TAG=$OPTARG
      ;;
    s)
      DO_SWITCH=true
      ;;
    i)
      DO_INSTALL=true
      ;;
    \?)
      echo "Invalid option: -${OPTARG}"
      exit 1
      ;;
  esac
done

set -e

echo "*** Fetching origin tags..."
git fetch origin && git fetch --tags
[[ -z ${TAG} ]] && TAG=$(git tag -l --sort "v:refname" | tail -n 1)
[[ -z `git tag -l ${TAG}` ]] && echo "*** Tag not found: ${TAG}" && exit 1

RELEASE=${RELEASE_DIR}/${TAG}

if [[ ! -d ${RELEASE} ]]; then
    WORKSPACE="/tmp/$(date +%Y%m%d%H%M%S)"
    echo "*** Building tag ${TAG}"
    mkdir ${WORKSPACE}
    git archive --format=tar ${TAG} | sudo tar -x -C ${WORKSPACE}
    pushd ${WORKSPACE} > /dev/null
    composer install --no-dev
    composer dump-autoload -o
    echo ${TAG} > VERSION
    printf "${TAG} ${USER}@${HOSTNAME} `date`\n\n`uname -a`\n\n`php -v`\n\n`composer -V`" > RELEASE
    popd > /dev/null
    sudo mv ${WORKSPACE} ${RELEASE}
else
    echo "*** Release already exists. Skipping the build."
fi

if [[ "$DO_SWITCH" = true ]]; then
    echo "*** Switching to tag ${TAG}"
    sudo ln -sfT ${RELEASE} /www/current
    for SERVICE in php7.0-fpm nginx; do
        sudo service ${SERVICE} restart
    done
fi

if [[ "$DO_INSTALL" = true ]]; then
    echo "*** Installing"
    pushd ${CURRENT}
    sudo -u www-data APP_ENV=prod bin/db.php migrations:migrate --no-interaction
    popd
fi
