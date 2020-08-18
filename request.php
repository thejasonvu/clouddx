<?php
    class Request
    {
        /*
        * Holds the URL of the current request
        */
        public $url;
        /*
        * Holds the trailing part of the URL after the index file but before the query string
        * http://www.example.com/php/index.php/some/stuff?foo=bar, then $path = /some/stuff
        */
        public $path;

        public function __construct()
        {
            $this->url = $_SERVER["REQUEST_URI"];
            $this->path = (isset($_SERVER["PATH_INFO"])? $_SERVER["PATH_INFO"] : "");
        }
    }

?>