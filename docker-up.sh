#!/usr/bin/env bash

CURRENT_UID=$(id -u):$(id -g) docker-compose --file=./../bbs-docker/docker-compose.yml up bbs-order