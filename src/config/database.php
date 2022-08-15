<?php
/**
* Database connection settings.
*
* Each array is a separate group, which can be connected to independently.
*
* The standard connection used by {@see Pdb} is the 'default' group, but
* the method {@see Pdb::connect} can be used to connect to other groups
*
* Group Options:
*  connection      Array of connection specific parameters:
*       type       Only supported value is 'mysql'
*       host       Hostname
*       user       Username
*       pass       Password
*       port       If non-empty, specifies a non-standard port
*       database   Database name
*  character_set   Database character set
**/

// Test server config
$config['default'] = [
    'connection' => [
        'type' => getenv('SITES_DB_TYPE') ?: 'mysql',
        'user' => getenv('SITES_DB_USERNAME') ?: 'sprout3',
        'pass' => getenv('SITES_DB_PASSWORD') ?: 'password',
        'database' => getenv('SITES_DB_DATABASE') ?: 'sprout3',
        'host' => getenv('SITES_DB_HOSTNAME') ?: 'localhost',
        'port' => FALSE,
    ],
    'prefix' => 'sprout_',
    'character_set' => 'utf8',
    'session' => [
        'sql_mode' => 'NO_ENGINE_SUBSTITUTION',
    ],
];

// This key is not secure, so it must not be used in production environments
$config['server_key'] = 'NOT SECURE';

