<?php

namespace Interpro\FileAggr\Db;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Settings\FileSetting as FileSettingInterface;
use Interpro\FileAggr\Contracts\Db\FileAggrDbAgent as FileAggrDbAgentInterface;

class TestFileAggrDbAgent implements FileAggrDbAgentInterface
{
    private $files = [];

    public function setFiles($files = [])
    {
        $this->files = $files;
    }

    /**
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     */
    public function ownerExist(ARef $aRef)
    {
        return true;
    }

    /**
     * @param string $owner_name
     * @param string $owner_id
     * @param string $file_name
     *
     * @return bool
     */
    public function fileExist($owner_name, $owner_id, $file_name)
    {

        foreach($this->files as $file_array)
        {
            if($file_array['name'] === $file_name and $file_array['entity_name'] === $owner_name and $file_array['entity_id'] === $owner_id)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $owner_name
     * @param string $owner_id
     * @param string $file_name
     * @param string $attr_name
     * @param mixed $value
     *
     * @return bool
     */
    public function fileAttrEq($owner_name, $owner_id, $file_name, $attr_name, $value)
    {
        foreach($this->files as $file_array)
        {
            if($file_array['name'] === $file_name and $file_array['entity_name'] === $owner_name and $file_array['entity_id'] === $owner_id)
            {
                if(array_key_exists($attr_name, $file_array))
                {
                    return ($file_array[$attr_name] === $value);
                }
            }
        }

        return false;
    }

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     * @param array $attrs
     *
     * @return void
     */
    public function fileToDb(ARef $aRef, FileSettingInterface $fileSetting, $attrs = [], $init = false)
    {
        $owner_name = $aRef->getType()->getName();
        $file_name = $fileSetting->getName();
        $owner_id = $aRef->getId();

        $file_array = null;

        foreach($this->files as & $value_array)
        {
            if($value_array['name'] === $file_name and $value_array['entity_name'] === $owner_name and $value_array['entity_id'] === $owner_id)
            {
                $file_array = & $value_array;
            }
        }

        if($file_array !== null)
        {
            if(array_key_exists('title', $attrs))
            {
                $file_array['title'] = $attrs['title'];
            }

            if(array_key_exists('link', $attrs))
            {
                $file_array['link'] = $attrs['link'];
            }
        }
    }

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     *
     * @return void
     */
    public function deleteFile(ARef $aRef, FileSettingInterface $fileSetting)
    {
        $id = $aRef->getId();
        $entity = $fileSetting->getEntityName();
        $name = $fileSetting->getName();

        foreach($this->files as $key => $file_array)
        {
            if($file_array['name'] === $name and $file_array['entity_name'] === $entity and $file_array['entity_id'] === $id)
            {
                unset($this->files[$key]);

                break;
            }
        }
    }

}
