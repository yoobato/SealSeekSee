<?php

$app['db.options'] = array(
    'driver' => 'pdo_mysql',
    'host' => 'DB Host',
    'user' => 'DB Username',
    'password' => 'DB Password',
    'dbname' => 'sealseeksee',
    'charset' => 'utf8',
);

$app['cache.namespace'] = 'sealseeksee_v1';

$app['debug'] = true;

define('LETTER_CHECK_SHORTEN_URL', 'http://bit.ly/2NBNmJu');

// Cafe24 SMS
define('CAFE24_SMS_USER_ID', 'User ID');
define('CAFE24_SMS_SECURE', 'API Key');
define('CAFE24_SMS_SENDER_PHONE', 'Sender phone number');

// What 3 Words
define('WHAT_3_WORDS_API_KEY', 'What 3 Words API Key');
