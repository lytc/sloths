<?php

if (defined('SLOTHS_APPLICATION_ENV') && $env = constant('SLOTHS_APPLICATION_ENV')) {
    return $env;
}

if ($env = getenv('SLOTHS_APPLICATION_ENV')) {
    return $env;
}

$developmentServerNames = [
    'sloths-application.dev',
];

if ($_SERVER && isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], $developmentServerNames)) {
    return 'development';
}

return 'production';