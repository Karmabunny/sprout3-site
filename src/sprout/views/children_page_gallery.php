<?php
/*
 * kate: tab-width 4; indent-width 4; space-indent on; word-wrap off; word-wrap-column 120;
 * :tabSize=4:indentSize=4:noTabs=true:wrap=false:maxLineLen=120:mode=php:
 *
 * Copyright (C) 2015 Karmabunny Pty Ltd.
 *
 * This file is a part of SproutCMS.
 *
 * SproutCMS is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * For more information, visit <http://getsproutcms.com>.
 */

use Sprout\Helpers\Enc;
use Sprout\Helpers\File;


$idx = 0;

echo '<ul class="children-gallery-list -clearfix">';
foreach ($page_node->children as $page) {
    if ($hide_blanks) {
        if (empty($page['gallery_thumb'])) continue;
        if (!File::exists($page['gallery_thumb'])) continue;
    }

    $mod = $idx++ % 4;

    echo "<li class=\"children-gallery-list-item children-gallery-item-mod{$mod}\">";
    echo "<a href=\"", Enc::html($page->getFriendlyUrl()), "\" class=\"children-gallery-list-item-link\">";

    if (!empty($page['gallery_thumb']) and File::exists($page['gallery_thumb'])) {
        echo '<img src="', Enc::html(File::resizeUrl($page['gallery_thumb'], 'c300x260')), '" class="children-gallery-list-item-image">';
    } else {
        echo '<img src="SKIN/images/children-gallery-thumb-placeholder.png" class="children-gallery-list-item-image children-gallery-list-item-image-placeholder">';
    }

    echo "<p class=\"children-gallery-list-item-title\">", Enc::html($page->getNavigationName()), "</p>";
    echo "<p class=\"children-gallery-list-item-anchor\"><span class=\"button\">Visit page</span></p>";

    echo "</a>";
    echo "</li>";
}

echo "</ul>";

echo "<script>";
echo "$(document).ready(function(){";
echo "$('.children-gallery-list-item').matchHeight();";
echo "});";
echo "</script>";