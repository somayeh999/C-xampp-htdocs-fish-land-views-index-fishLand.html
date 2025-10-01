<?php
class Controller{
    public $model;
    function __construct()
    {
    }
    function view($viewUrl,$data=[],$baseTag=true,$headTag=true,$showHeader=true,$showFooter=true){
        if ($baseTag==true){
            require ("baseTag.php");
        }
        if ($headTag==true){
            $arr=explode("/",$viewUrl);
            array_pop($arr);
            $str=implode("/",$arr);
            require ("views/$str/headTag.php");
            echo '</head>';

        }
        if ($showHeader==true){
            echo '<body>';
            require ("header.php");
        }
        require ("views/$viewUrl.php");
        if ($showFooter==true){
            require ("footer.php");
        }
    }
    function model($modelUrl){
        require "models/model_$modelUrl.php";
        $className="model_".$modelUrl;
        $this->model=new $className;
    }
}