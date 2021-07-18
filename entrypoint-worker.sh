#!/bin/bash
cd /app && composer install && php ./bin/console enqueue:consume -vvv