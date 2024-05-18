<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration([__DIR__."/src"], $isDevMode);

$dbParams = [
    'driver'   => 'pdo_mysql',
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'carmaster_db',
    'user'     => 'carmaster_user',
    'password' => 'carmaster123',
];

$entityManager = EntityManager::create($dbParams, $config);
