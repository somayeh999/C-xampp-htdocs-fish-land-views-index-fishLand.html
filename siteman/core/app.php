<?php

class App
{
    public $controller = "index";
    public $method = "index";
    public $params = [];

    function __construct()
    {
        if (isset($_GET["url"])) {
            $url = $this->parseUrl($_GET["url"]);
            $this->controller = str_replace("-","_",$url[0]);
            unset($url[0]);

        }
        $c= strtolower($this->controller);
        $controllerUrl = "controllers/$c.php";

        if (file_exists($controllerUrl)) {
            require($controllerUrl);
            $controllerObj = new $c;
            $controllerObj->model($c);

            if (isset($url[1])){
                $m=str_replace("-","_",$url[1]);
                if (method_exists($controllerObj, $m)){
                    $this->method = $m;
                    unset($url[1]);
                }
                $this->params = array_values($url);
            }
            if (method_exists($controllerObj, $this->method)) {
                call_user_func_array([$controllerObj, $this->method], $this->params);
            }else{
                $this->res404();

            }
        }else{
            $this->res404();

        }


    }
    function res404(){
        http_response_code(404);
        include('404.html'); // provide your own HTML for the error page
        die();
    }

    function parseUrl($url)
    {
//        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = rtrim($url, "/");
        return explode("/", $url);
    }
}
