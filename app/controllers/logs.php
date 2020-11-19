<?php

require '../app/models/get_logs.php';

$logs_vars['logs'] = get_logs(25);

$main_vars['content'] = $f->merge_template('logs', $logs_vars);

$main_vars['title'] = 'Request Log';

echo $f->merge_template('main', $main_vars);
