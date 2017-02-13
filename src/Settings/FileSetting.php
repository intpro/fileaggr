<?php

namespace Interpro\FileAggr\Settings;

use Interpro\FileAggr\Collections\ExtsCollection;
use Interpro\FileAggr\Contracts\Settings\FileSetting as FileSettingInterface;

class FileSetting implements FileSettingInterface
{
    protected $entity_name;
    protected $name;
    protected $exts;

    public function __construct($entity_name, $name, ExtsCollection $exts)
    {
        $this->entity_name = $entity_name;
        $this->name        = $name;
        $this->exts        = $exts;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entity_name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Interpro\FileAggr\Collections\ExtsCollection
     */
    public function getExts()
    {
        return $this->exts;
    }

    /**
     * @return \Interpro\FileAggr\Settings\Extension
     */
    public function getExt()
    {
        return $this->exts->first();
    }

    /**
     * @return bool
     */
    public function extAvailable($ext_name)
    {
        if($this->exts->count() === 0 or $this->exts->exist('all'))
        {
            return true;
        }
        else
        {
            return $this->exts->exist($ext_name);
        }
    }

    /**
     * @param string $req_name
     *
     * @return mixed
     */
    public function __get($req_name)
    {
        if($req_name === 'entity_name')
        {
            return $this->entity_name;
        }
        elseif($req_name === 'name')
        {
            return $this->name;
        }
        else
        {
            return null;
        }
    }

}
