<?php

$app['db.options'] = array(
    'driver' => 'pdo_mysql',
    'host' => 'yoobato.vps.phps.kr',
    'user' => 'sealseeksee',
    'password' => '비밀번호',
    'dbname' => 'sealseeksee',
    'charset' => 'utf8',
);

$app['cache.namespace'] = 'sealseeksee_v1';

$app['debug'] = true;

define('SEALSEEKSEE_API_BASE_URL', 'http://sealseeksee.yoobato.com');
