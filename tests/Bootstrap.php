<?php

// executes the "php bin/console cache:clear" command
passthru(sprintf(
    'php "%s/../bin/console" d:d:dr --force',
    __DIR__
));

passthru(sprintf(
    'php "%s/../bin/console" d:d:cr',
    __DIR__
));

passthru(sprintf(
    'php "%s/../bin/console" d:mi:mi',
    __DIR__
));

//passthru(sprintf(
//    'php "%s/../bin/console" d:f:l',
//    __DIR__
//));

require __DIR__.'/../vendor/autoload.php';
