#!/usr/bin/env bash

set -eu

readonly BIN_SCRIPTS_PATH="${PROJECT_DIR}/bin/scripts"
readonly DOCKER_CONTAINER_APP_PHP=duralas_messages_rp_app_dev_php
readonly DOCKER_CONTAINER_APP_WORKDIR=/var/www/html

function titleAction() {
    printf "\e[35m${1}\e[0m\n"
}

function error() {
    printf "\e[41m${1}\e[0m\n"
}

function warning() {
    printf "\e[33m${1}\e[0m\n"
}

function dockerisePhp() {
    if [ ${isInDocker} == false ]; then
        return
    fi

    if [ -z "${BIN_DIR-}" ]; then
        BIN_DIR="bin"
    fi

    titleAction "Exec ${DOCKER_CONTAINER_APP_PHP}"
    docker \
        exec \
            --tty \
            --interactive \
            --user "$(id -u)":"$(id -g)" \
            --workdir "${DOCKER_CONTAINER_APP_WORKDIR}" \
            "${DOCKER_CONTAINER_APP_PHP}" \
            "${BIN_DIR}"/"$(basename "${0}")" \
            ${scriptParameters}
    exit 0
}

if [ $(which docker || false) ]; then
    readonly isInDocker=true
else
    readonly isInDocker=false
fi
export isInDocker

# Parameters
clearCache=false
workingFiles=
isTty=true
refresh=false
scriptParameters=
for param in "${@}"; do
    if [ "${param}" == "--clear-cache" ]; then
        clearCache=true
        continue
    elif [ "${param:0:7}" == "--file=" ]; then
        workingFiles+=" ${param:7}"
        # Give --file= as $scriptParameters unless if in docker container
        if [ ${isInDocker} == false ]; then
            continue
        fi
    elif [ "${param}" == "--no-tty" ]; then
        isTty=false
        continue
    elif [ "${param}" == "--refresh" ]; then
        refresh=true
        continue
    fi

    scriptParameters="${scriptParameters} ${param}"
done
export clearCache
export workingFiles
export isTty
export refresh
export scriptParameters
