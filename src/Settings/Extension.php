<?php

namespace Interpro\FileAggr\Settings;

use Interpro\Core\Contracts\Named;

class Extension implements Named
{
    protected $name;
    protected $descr;
    protected $pathResolver;

    public function __construct($ext_name, $descr, PathResolver $pathResolver)
    {
        $this->name  = $ext_name;
        $this->descr = $descr;
        $this->pathResolver = $pathResolver;
    }

    /**
     * @return string
     */
    public function getDescr()
    {
        return $this->descr;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $req_name
     *
     * @return mixed
     */
    public function __get($req_name)
    {
        if($req_name === 'name')
        {
            return $this->name;
        }
        elseif($req_name === 'descr')
        {
            return $this->descr;
        }
        elseif($req_name === 'icon')
        {
            return $this->pathResolver->getIconPath().'/'.$this->name.'_icon.jpg';
        }
        else
        {
            return null;
        }
    }

}
