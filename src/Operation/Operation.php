<?php

namespace Interpro\FileAggr\Operation;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Db\FileAggrDbAgent as FileAggrDbAgentInterface;
use Interpro\FileAggr\Exception\FileAggrException;
use Interpro\FileAggr\Contracts\Settings\FileSetting as FileSettingInterface;
use Interpro\FileAggr\Contracts\Settings\PathResolver as PathResolverInterface;
use Interpro\Core\Contracts\Taxonomy\Taxonomy as TaxonomyInterface;

abstract class Operation
{
    protected $fileType;
    protected $resizeType;
    protected $cropType;

    protected $pathResolver;
    protected $taxonomy;
    protected $dbAgent;
    protected $phAgent;

    protected $mime_types;

    public function __construct(PathResolverInterface $pathResolver,
                                TaxonomyInterface $taxonomy,
                                FileAggrDbAgentInterface $fileAggrDbAgent)
    {
        $this->pathResolver = $pathResolver;
        $this->taxonomy = $taxonomy;
        $this->dbAgent = $fileAggrDbAgent;

        $this->fileType = $this->taxonomy->getType('file');

        $this->mime_types = [
            'gif'=>'image/gif',
            'jpeg'=>'image/jpeg',
            'png'=>'image/png',
            'svg'=>'image/svg+xml',
            'txt'=>'text/plain',
            'xml'=>'text/xml',
            'html'=>'text/html'
        ];
    }

    protected function getExtension($mime)
    {
        $ext = array_search($mime, $this->mime_types);

        if(!$ext)
        {
            //throw new FileAggrException('Попытка получить расширение файла для не поддерживаемого типа '.$mime.'!');
        }

        return $ext;
    }

    protected function checkOwner(ARef $aRef)
    {
        if(!$this->dbAgent->ownerExist($aRef))
        {
            throw new FileAggrException('Сущность '.$aRef->getType()->getName().'('.$aRef->getId().') не найдена!');
        }
    }

    protected function deleteAllFilesByPath($path)
    {
        foreach (glob($path.'*.*') as $file)
        {
            if(is_dir($file))
            {
                continue;
            }
            unlink($file);
        }
    }

    protected function deleteAllFiles(ARef $aRef, FileSettingInterface $fileSetting, $tmp = false)
    {
        if($tmp)
        {
            $files_dir = $this->pathResolver->getTmpDir();
        }
        else
        {
            $files_dir = $this->pathResolver->getFileDir();
        }

        if (!is_writable($files_dir))
        {
            throw new FileAggrException('Дирректория файлов ('.$files_dir.') не доступна для записи!');
        }

        $file_prefix = $this->getFilePrefix($aRef->getType()->getName(), $aRef->getId(), $fileSetting->getName());

        $this->deleteAllFilesByPath($files_dir.'/'.$file_prefix);
    }

    protected function getFilePrefix($owner_name, $owner_id, $file_name)
    {
        return $owner_name.'_'.$owner_id.'_'.$file_name;
    }

    protected function getNamedAttrs($name, & $attrs)
    {
        $item_attrs = [];

        if(array_key_exists($name, $attrs))
        {
            $item_attrs = & $attrs[$name];
        }

        return $item_attrs;
    }

}
