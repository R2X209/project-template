<?php

function get_logs($count = 'all') {
    global $f; 

    if ($count == 'all') {
        $recordset = $f->db_get_recordset("SELECT * FROM requests ORDER BY request_id DESC", 'logs');  
    } else {
        $recordset = $f->db_get_recordset("SELECT * FROM requests ORDER BY request_id DESC LIMIT ".$count, 'logs'); 
    }

    return $recordset;
} 
