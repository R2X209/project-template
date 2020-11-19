<?php

class Framework 
{
    private $root_url;
    private $root_path;
    private $controller;
    private $token;
    private $settings = array();
    private $url_arguments = array();

    function __construct() {
        $this->root_path = str_replace('/app/libraries/framework.php', '', __FILE__);
        $this->root_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'];
        $this->settings = parse_ini_file($this->root_path.'/app/settings.ini', true);
        $this->load_url_arguments();
        $this->set_token();
        $this->log_visit();
        $this->set_controller();
    }

    function __get($name) {
        return $this->$name;
    }

    function load_url_arguments() {
        $args = explode('/', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));

        foreach($args as $arg) {
            if(strpos($arg, '.') !== false)
            {
                $key_value = explode('.', $arg);
                $this->url_arguments[$key_value[1]] = $key_value[0];
            }
        }
    }

    function set_token() {
        if(!isset($_COOKIE['token'])) {
            $this->token = md5(uniqid(rand(), true));
            setcookie('token', $this->token, time()+311040000, '/');
        } else {
            $this->token = $_COOKIE['token'];
        }
    }

    function log_visit() {
        $query = "INSERT INTO requests VALUES (
                        NULL,
                        '".date("Y-m-d H:i:s")."', 
                        '".$_SERVER['REMOTE_ADDR']."', 
                        '".$_SERVER['REQUEST_URI']."', 
                        '".$_SERVER['HTTP_USER_AGENT']."', 
                        '".$this->token."'
                    )";
        
        $this->db_execute($query, 'logs');
    }

    function set_controller() {
        if (isset($this->url_arguments['ctl'])) {
            $this->controller = $this->url_arguments['ctl'];
        } else {
            if (isset($this->settings['default_controller'])) {
                $this->controller =  $this->settings['default_controller'];
            } else {
                $this->controller =  'logs';
            }
        }
    }

    function get_controller_file_path() {
        return $this->root_path.'/app/controllers/'.$this->controller.'.php';
    }

    function get_pages_folder_path() {
        return $this->root_path.'/public/content/pages';
    }

    function get_url_argument($arg) {
        if (isset($this->url_arguments[$arg])) {
            return $this->url_arguments[$arg];
        } else {
            return false;
        }
    }

    function count_url_arguments() {
        return count($this->url_arguments);
    }

    function get_setting($item) {
        if (isset($this->settings[$item])) {
            return $this->settings[$item];
        } else {
            return false;
        }
    }

    function merge_template($file, $vars = array()) {
        $root = $this->root_url;
        extract($vars);

        ob_start();
            require($this->root_path.'/app/templates/'.$file.'.html');
            $html = ob_get_contents();
        ob_end_clean();

        return($html);
    }

    function merge_page($page, $vars = array()) {
        $root = $this->root_url;
        $page_root = $root.'/content/pages/'.$page;
        extract($vars);

        ob_start();
            require($this->root_path.'/public/content/pages/'.$page.'/content.html');
            $html = ob_get_clean();
        ob_end_clean();

        return($html);
    }

    function db_execute($query, $database = 'primary') {
        $db = new SQLite3($this->root_path.'/app/databases/'.$this->settings['databases'][$database]['filename']);
        $query = $this->db_clean_query($query);

        return $db->exec($query);
    }

    function db_get_record($query, $database = 'primary') {
        $db = new SQLite3($this->root_path.'/app/databases/'.$this->settings['databases'][$database]['filename']);
        $query = $this->db_clean_query($query);

        return $db->querySingle($query, true);
    }

    function db_get_recordset($query, $database = 'primary') {
        $db = new SQLite3($this->root_path.'/app/databases/'.$this->settings['databases'][$database]['filename']);
        $query = $this->db_clean_query($query);
        $recordset = $db->query($query);

        while($row = $recordset->fetchArray(SQLITE3_ASSOC)) {
            $recordset_array[] = $row;
        }

        return $recordset_array;
    }

    function db_clean_query($query) { 
        return str_replace(';', '', $query);
    }

    function print_array($array) {
        $html = print_r($array, true);
        return '<pre>'.$html.'</pre>';
    }

}
