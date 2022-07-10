<?php
return [
    'secret' => getenv('SECRET') ? getenv('SECRET') : 'opensesame',
    'names' => getenv('NAMES') ? getenv('NAMES') : 'Alice,Bob',
    'baseFolder' => getenv('BASE_FOLDER') ? getenv('BASE_FOLDER') : '',
    'development' => getenv('DEVELOPMENT') ? getenv('DEVELOPMENT') : 'false',
    'noindex' => getenv('NOINDEX') ? getenv('NOINDEX') : 'true',
    'new_appreciation_every_x_seconds' => getenv('NEW_APPRECIATION_EVERY_X_SECONDS') ? getenv('NEW_APPRECIATION_EVERY_X_SECONDS') : (1 * DAY_IN_SECONDS),
];