<?php
/*
 * kate: tab-width 4; indent-width 4; space-indent on; word-wrap off; word-wrap-column 120;
 * :tabSize=4:indentSize=4:noTabs=true:wrap=false:maxLineLen=120:mode=php:
 *
 * Copyright (C) 2016 Karmabunny Pty Ltd.
 *
 * This file is a part of SproutCMS.
 *
 * SproutCMS is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation, either
 * version 2 of the License, or (at your option) any later version.
 *
 * For more information, visit <http://getsproutcms.com>.
 */
use Sprout\Helpers\Csrf;
use Sprout\Helpers\Form;


Form::setData(['controllers' => $enabled]);
?>

<form method="post" action="admin/call/per_record_permission/save">
    <?php echo Csrf::token(); ?>

<?php
Form::nextFieldDetails('Please select which tabs should have per-record permissions enabled', false);
echo Form::checkboxSet('controllers', [], $controllers);
?>

<p><input type="submit" class="button" value="Save"></p>
</form>
