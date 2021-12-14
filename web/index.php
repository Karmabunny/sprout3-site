<?php
/*
 * Copyright (C) 2017 Karmabunny Pty Ltd.
 *
 * This file is a part of SproutCMS.
 *
 * SproutCMS is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation, either
 * version 2 of the License, or (at your option) any later version.
 *
 * For more information, visit <http://getsproutcms.com>.
 */

// A big bunch of core constants.
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', BASE_PATH . 'vendor' . DIRECTORY_SEPARATOR);
define('STORAGE_PATH', BASE_PATH . 'storage' . DIRECTORY_SEPARATOR);
define('DOCROOT', BASE_PATH . 'src' . DIRECTORY_SEPARATOR);
define('KOHANA', basename(__FILE__));

ini_set('display_errors', '1');

// This file contains a class with a methods for determining the details of
// the very initial environment, prior to the rest of the system coming up
require DOCROOT . 'config/_bootstrap_config.php';

// Define the website environment status.
if (file_exists(DOCROOT . 'config/dev_hosts.php')) {
    require DOCROOT . 'config/dev_hosts.php';
}

// The composer autoloader.
require VENDOR_PATH . 'autoload.php';

// Load up.
require VENDOR_PATH . 'karmabunny/sprout/src/bootstrap.php';
