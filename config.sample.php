<?php
return [
    'secret' => getenv('SECRET') ? getenv('SECRET') : 'opensesame',
    'names' => getenv('NAMES') ? getenv('NAMES') : 'Alice,Bob',
    'baseFolder' => getenv('BASE_FOLDER') ? getenv('BASE_FOLDER') : '',
    'development' => getenv('DEVELOPMENT') ? getenv('DEVELOPMENT') : 'false',
    'noindex' => getenv('NOINDEX') ? getenv('NOINDEX') : 'true',
];