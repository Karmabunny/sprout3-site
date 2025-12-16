#!/usr/bin/env bash
set -e

cd "$(dirname $0)"

export FPM_USER="$(id -u)"
export FPM_GROUP="$(id -g)"
export PROJECT=sprout

if [[ $1 ]]; then
    CMD="$1"
    shift 1
else
    CMD="help"
fi

if which docker-compose; then
    COMPOSE="docker-compose --project-name $PROJECT"
else
    COMPOSE="docker compose --project-name $PROJECT"
fi

case "$CMD" in
    "up")
        $COMPOSE up \
            --build \
            --remove-orphans \
            --detach \
            $@
        exit $?
    ;;

    "composer")
        $COMPOSE exec app composer $@
        exit $?
    ;;

    "sprout")
        $COMPOSE exec app php ./web/index.php $@
        exit $?
    ;;

    "shell"|"sh")
        $COMPOSE exec ${1:-app} bash
        exit $?
    ;;

    "sql")
        $COMPOSE exec mysql mysql -u sprout3
        exit $?
    ;;

    "help")
        echo "One of:"
        echo " - up [...images]"
        echo " - sprout [...args]"
        echo " - composer [...args]"
        echo " - shell <image>"
        echo " - sql"
        exit 1
    ;;

    *)
        $COMPOSE $CMD $@
        exit $?
    ;;
esac
