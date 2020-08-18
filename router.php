<?php

class Router
{

    static public function parse($url, $request, $path)
    {
        $url = trim($url);
        $url = basename($url);

        // Route the URL to the specified fields

        // Default Route = folder name or index.php
        if ($url == "clouddx2" || $url == "index.php")
        {
            $request->controller = "home"; // Default controller
            $request->action = "index";
            $request->params = [];
        }
        // Grabs the path after the index.php and routes it to proper variables
        else
        {
            $path = trim($path);
            $path = ltrim($path,'/');

            $explode_url = explode('/', $path);
            $request->controller = strtolower($explode_url[0]);
            $request->action = strtolower(($explode_url[1]?$explode_url[1] : 'index'));
            $request->params = array_slice($explode_url, 2);
        }

    }
}
?>