#!/bin/bash
docker exec -ti bbs-order sh -c "cd /app && ./bin/console doctrine:migrations:migrate --no-interaction"
