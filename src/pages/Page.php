<?php

namespace HcodeEcom\pages;

use Rain\Tpl;

class Page{
    
    private $tpl;
    private $options = [];
    private $defaults = [
        "data" => []
    ];

    public function setData($data = []){
        foreach($data as $key => $value){
            $this->tpl->assign($key, $value);
        }
    }

   
    public function __construct($opts = [], $tplDir = '/views/')
    {
        
        $this->options = array_merge($this->defaults, $opts);
        $config = [
            "tpl_dir"   => $_SERVER['DOCUMENT_ROOT'].$tplDir,
            "cache_dir" => $_SERVER['DOCUMENT_ROOT']."/views-cache/",
            "debug"     => false
        ];
       
        Tpl::configure( $config );

        $this->tpl = new Tpl;

        $this->setData($this->options["data"]);

        $this->tpl->draw("header");
    }
 

    public function setTpl($name, $data = [], $returnHtml = false)
    {
        $this->setData($data);

        return $this->tpl->draw($name, $returnHtml);
    }

    public function __destruct()
    {
        $this->tpl->draw("footer");
    }
}
