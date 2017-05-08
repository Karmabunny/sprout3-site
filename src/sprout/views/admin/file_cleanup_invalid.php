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

use Sprout\Helpers\Csrf;
?>


<?php
if ($count_delete == 0) {
    echo '<div class="info">There are no files which require cleanup.</div>';
    return;
}
?>


<form action="SITE/admin/call/file/cleanupInvalidAction" method="post">
    <?php echo Csrf::token(); ?>

    <div class="message-bar-warning">
        <p>This will remove file references from the database that are invalid.</p>
        <p>This is an irreversible action.</p>
    </div>

    <p><b><?php echo $count_delete; ?> records</b> will be deleted.</p>

    <div class="action-bar">
        <button type="submit" class="button">Cleanup invalid files</button>
    </div>
</form>
