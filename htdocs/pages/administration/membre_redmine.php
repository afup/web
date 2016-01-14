<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

header('Location: https://github.com/afup/web/issues?state=open');
