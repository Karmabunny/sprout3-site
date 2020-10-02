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

/**
 * Kohana process control file, loaded by the front controller.
 *
 * $Id: Bootstrap.php 4409 2009-06-06 00:48:26Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */

use Sprout\Helpers\Notification;
use Sprout\Helpers\PageRouting;
use Sprout\Helpers\Router;
use Sprout\Helpers\Ssl;
use Sprout\Helpers\SubsiteSelector;
use Sprout\Helpers\Sprout;
use Sprout\Helpers\Url;


// Load core files
require APPPATH . 'core/utf8.php';
require APPPATH . 'core/Event.php';
require APPPATH . 'core/Kohana.php';


// Protect against XXE attacks by disallowing external entities to be loaded
// See also https://en.wikipedia.org/wiki/XML_external_entity_attack
// PHP-8+ deprecated this because it's disabled by default.
if (PHP_VERSION_ID < 80000) {
    libxml_disable_entity_loader(true);
}

// Prepare the environment (inc. error/exception handling, output buffering, and auto-loader)
Kohana::setup();

// Determine the URI (stored in Router::$current_uri)
Router::findUri();

// Redirect to alternate hostname and/or protocol if requred
Router::originCleanup();

// Mini verion of framework when using the welcome system
// that avoids lots of code paths which use a database.
if (Sprout::moduleInstalled('Welcome')) {
    Kohana::disableCache();
    if (Router::$current_uri === '' or strpos(Router::$current_uri, 'welcome/') === 0) {
        Router::setup();
        Kohana::instance();
        Event::run('system.shutdown');
        exit(1);
    }
} else {
    // If the user has just finished setting up
    if (strpos(Router::$current_uri, 'welcome/checklist') === 0) {
        Notification::error('Welcome-Module not enabled! Enable it via <a href="http://docs.getsproutcms.com/installation">config file</a>.', 'html');
        Notification::confirm('Or please log in to admin area using the form below.');
        Url::redirect('admin/');
    }
}

// Choose the subsite to use, based on domain, directory, mobile etc.
SubsiteSelector::selectSubsite();

// Any redirects etc before the Kohana URLs
require APPPATH . '/sprout_load.php';
PageRouting::prerouting();

// Kohana routes and controller/method URLs
// Key vars are Router::$controller and Router::$method
Router::setup();

// Postrouting such as page URLs
PageRouting::postrouting();

// Check if this controller requires SSL
Ssl::check();

// 404?
if (Router::$controller === NULL) {
    Event::run('system.404');
}

// Run the method
Kohana::instance();

// Clean up and exit
Event::run('system.shutdown');

