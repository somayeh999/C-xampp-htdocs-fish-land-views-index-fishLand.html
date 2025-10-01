<?php
class Index extends Controller
{
    function __construct()
    {
    }
    function index()
    {
        $x=[];
        $this->view("index/index",$x);

	}


}
?>