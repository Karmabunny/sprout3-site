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

namespace Sprout\Helpers;

use Kohana_Exception;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Extension\DebugExtension;

/**
 * Renderer for twig engine
 *
 * @todo - There's lots of opportunity to cache these templates.
 */
class TwigView extends View
{
    protected static $EXTENSION = '.twig';

    /** @var Environment */
    protected static $twig;

    /** @var TwigSkinLoader */
    protected static $loader;

    /** @var string */
    protected $kohana_template_name;


    /** @inheritdoc */
    public function __construct($name, array $data = [])
    {
        // Initialise the twig renderer.
        if (!isset(self::$twig)) {
            self::$loader = new TwigSkinLoader();
            self::$twig = new Environment(self::$loader, [
                'debug' => !IN_PRODUCTION,
                'strict_variables' => !IN_PRODUCTION,
            ]);

            if (!IN_PRODUCTION) {
                self::$twig->addExtension(new DebugExtension());
            }

            self::$twig->addExtension(new SproutExtension());
        }

        $this->kohana_template_name = $name;
        parent::__construct($name, $data);
    }


    /** @inheritdoc */
    public function render($print = FALSE, $renderer = FALSE)
    {
        if (empty($this->kohana_filename)) {
            throw new Kohana_Exception('core.view_set_filename');
        }

        $output = self::$twig->render($this->kohana_template_name, $this->kohana_local_data);

        if ($renderer !== FALSE AND is_callable($renderer, TRUE))
        {
            // Pass the output through the user defined renderer
            $output = call_user_func($renderer, $output);
        }

        if ($print === TRUE)
        {
            // Display the output
            echo $output;
            return;
        }

        return $output;
    }
}