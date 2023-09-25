#!/usr/bin/env bash

ENVIRONMENT=$1
if [ -z "$ENVIRONMENT" ]; then
    echo "Usage: up.sh <environment>"
    exit 1
fi

docker compose -f docker-compose.$ENVIRONMENT.yml up -d