<?php defined('BASEPATH') or die();
    if (function_exists('_t') === false) {
        function _t($line) {
            global $LANG;
            return ($t = $LANG->line($line)) ? $t : $line;
        }
    }
/* End of file */