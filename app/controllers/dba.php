<?php

if (! $f->get_setting('dba_password')) {
    exit();
}

require $f->root_path.'/app/libraries/phpliteadmin.php';
