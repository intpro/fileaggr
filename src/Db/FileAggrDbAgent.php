<?php

namespace Interpro\FileAggr\Db;

use Interpro\Core\Contracts\Mediator\RefConsistMediator;
use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Settings\FileSetting as FileSettingInterface;
use Interpro\FileAggr\Model\File;
use Interpro\FileAggr\Contracts\Db\FileAggrDbAgent as FileAggrDbAgentInterface;

class FileAggrDbAgent implements FileAggrDbAgentInterface
{
    private $refConsistMediator;

    /**
     * @return void
     */
    public function __construct(RefConsistMediator $refConsistMediator)
    {
        $this->refConsistMediator = $refConsistMediator;
    }

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     */
    public function ownerExist(ARef $aRef)
    {
        return $this->refConsistMediator->exist($aRef);
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

        $collection = File::where('entity_name', '=', $owner_name)->where('entity_id', '=', $owner_id)->where('name', '=', $file_name)->get();

        $file = null;

        //Удалим лишние записи по этому ключу, если есть (на всякий случай, т. к. записи в БД уникальны по авто инк. id)
        foreach($collection as $curr_file)
        {
            if($file === null)
            {
                $file = $curr_file;

                if($init)
                {
                    break;
                }
            }
            else
            {
                $file->delete();
            }
        }

        $db = !$init;

        if($file === null)
        {
            $file = new File;
            $file->title = '';
            $file->link = '';
            $db = true;
        }

        if($db)
        {
            if(!array_key_exists('title', $attrs))
            {
                $attrs['title'] = $file->title;
            }

            if(!array_key_exists('link', $attrs))
            {
                $attrs['link'] = $file->link;
            }

            $file->entity_name = $owner_name;
            $file->entity_id   = $owner_id;
            $file->name        = $file_name;
            $file->title       = $attrs['title'];
            $file->link        = $attrs['link'];

            $file->save();
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
        $owner_name = $aRef->getType()->getName();
        $owner_id = $aRef->getId();

        //Удалим все записи по этому ключу (на всякий случай, т. к. записи в БД уникальны по авто инк. id)
        File::where('entity_name', '=', $owner_name)->where('entity_id', '=', $owner_id)->where('name', '=', $fileSetting->getName())->delete();
    }

}
