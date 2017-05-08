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

use Exception;
use Sprout\Helpers\Enc;


/**
* Provided functions for the display of widgets
**/
class Widgets
{
    private static $widget_areas = array();

    /**
    * Add a widget to the list of widgets for a specific area
    *
    * @param int $area_id The widget area to add the widget to
    * @param string $name The name of the widget to add
    * @param array $settings The widget settings to use
    **/
    public static function add($area_id, $name, $settings)
    {
        if (! preg_match('/^[0-9]+$/', $area_id)) {
            $area = WidgetArea::findAreaByName($area_id);
            if (! $area) return;
            $area_id = $area->getIndex();
        }

        self::$widget_areas[$area_id][] = array($name, $settings);
    }

    /**
    * Remove a widget to the list of widgets for a specific area
    *
    * @param int $area_id The widget area to add the widget to
    * @param string $name The name of the widget to remove
    **/
    public static function remove($area_id, $name)
    {
        if (! preg_match('/^[0-9]+$/', $area_id)) {
            $area = WidgetArea::findAreaByName($area_id);
            if (! $area) return;
            $area_id = $area->getIndex();
        }

        foreach (self::$widget_areas[$area_id] as $idx => $def) {
            if ($def[0] === $name) unset (self::$widget_areas[$area_id][$idx]);
        }
    }

    /**
    * Create an instance of a specific widget in memory
    *
    * @param string $name The name of the widget to instantiate.
    * @return Widget The instance
    **/
    public static function instantiate($name)
    {
        $class = $name;
        if (substr($class, -6) != 'Widget') $class .= 'Widget';
        if (strpos($class, '\\') === false) {
            $class = 'Sprout\\Widgets\\' . $class;
        }
        if (!class_exists($class)) {
            throw new Exception("Unknown widget {$name}");
        }

        return new $class();
    }

    /**
     * Instantiate, import settings, and render
     * @param int $orientation See ORIENTATION_* constants in {@see WidgetArea}
     * @param string $name Class name of the widget
     * @param array $settings Widget settings (keys and values vary with widget subclass)
     * @param string $pre_html HTML to go before the rendered widget
     * @param string $post_html HTML to go after the rendered widget
     */
    public static function render($orientation, $name, array $settings, $pre_html = null, $post_html = null)
    {
        $inst = self::instantiate($name);
        if ($inst == null) return null;

        $inst->importSettings($settings);
        $html = $inst->render($orientation);
        if ($html == null) return null;

        if ($orientation != WidgetArea::ORIENTATION_EMAIL and AdminAuth::isLoggedIn()) {
            $infobox = true;
        }

        $title = Enc::html($inst->getTitle());

        if (! $pre_html) {
            $class = 'widget widget-' . Enc::id(str_replace('\\', '-', (isset($inst->classname) ? $inst->classname : $name)));
            if (!empty($infobox)) $class .= ' widget-hasinfobox';
            $class .= ' orientation-' . WidgetArea::$orientation_classes[$orientation];

            $pre_html = "<div class=\"{$class}\">";
        }

        if (! $post_html) $post_html = "</div>";

        $ret = '';
        $ret .= $pre_html;
        if ($title) $ret .= "<h3 class=\"widget-title\">{$title}</h3>";
        $ret .= $html;

        if (!empty($infobox)) {
            $ret .= self::infobox($inst);
        }

        $ret .= $post_html;

        return $ret;
    }

    /**
    * Render extra widget info for admins
    **/
    private static function infobox($inst)
    {
        $ret = '<div class="widget-infobox">';
        $ret .= '<i>' . Enc::html($inst->getFriendlyName()) . ' Addon</i> &nbsp; ';

        $info_lab = $inst->getInfoLabels();
        if ($info_lab) {
            foreach ($info_lab as $name => $val) {
                $ret .= '<b>' . Enc::html($name) . ':</b> ' . Enc::html($val) . ' &nbsp; ';
            }
        }

        $edit_url = $inst->getEditUrl();
        if ($edit_url) {
            $ret .= '<a href="SITE/' . $edit_url . '" target="_blank">Edit content</a>';
        }

        $ret .= '</div>';

        return $ret;
    }


    /**
     * Draw the widgets for a specific area
     *
     * The available widget areas are defined in the {@link /config/sprout.php} file.
     * Typically there are two areas defined, 'sidebar' and 'embedded'.
     *
     * @param string $area_name The name of the widget area to draw.
     * @return string HTML representing the rendered widgets
     */
    public static function renderArea($area_name)
    {
        $area = WidgetArea::findAreaByName($area_name);
        if ($area == null) {
            return;
        }

        $area_id = $area->getIndex();
        if (empty(self::$widget_areas[$area_id])) {
            return;
        }

        $out = '';
        foreach (self::$widget_areas[$area_id] as $widget_details) {
            list($name, $settings) = $widget_details;
            $out .= self::render($area->getOrientation(), $name, $settings);
        }

        return $out;
    }


    /**
     * Render a widget area, and output directly
     *
     * @deprecated Use {@see Widgets::renderArea} instead
     */
    public static function area($area_name)
    {
        echo self::renderArea($area_name);
    }


    /**
    * Does the specified widget area have widgets?
    *
    * @param string $area_name The name of the widget area to query.
    *
    * @tag api
    * @tag designer-api
    **/
    public static function hasWidgets($area_name)
    {
        $area = WidgetArea::findAreaByName($area_name);
        if ($area == null) return;
        $area_id = $area->getIndex();

        if (@count(self::$widget_areas[$area_id]) > 0) return true;

        return false;
    }

}


