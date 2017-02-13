<?php

namespace Interpro\FileAggr\Creation;

use Interpro\Core\Contracts\Taxonomy\Fields\OwnField;
use Interpro\FileAggr\Exception\FileAggrException;

class CapGenerator
{
    private $fileItemFactory;

    public function __construct()
    {
        $this->fileItemFactory = null;
    }

    public function setFactory(FileItemFactory $fileItemFactory)
    {
        $this->fileItemFactory = $fileItemFactory;
    }

    /**
     * @param \Interpro\Core\Contracts\Taxonomy\Fields\OwnField $field
     *
     * @return \Interpro\FileAggr\Items\FileItem
     */
    public function createFile(OwnField $field)
    {
        if(!$this->fileItemFactory)
        {
            throw new FileAggrException('Для генератора заглушек не установлена фабрика элементов!');
        }

        $name = $field->getName();

        $item  = $this->fileItemFactory->createFile($field, ['name' => $name], true);

        return $item;
    }

}
