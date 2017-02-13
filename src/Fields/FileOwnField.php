<?php

namespace Interpro\FileAggr\Fields;

use Interpro\Extractor\Contracts\Fields\OwnField as OwnFieldInterface;
use Interpro\Core\Contracts\Taxonomy\Fields\OwnField as OwnFieldMeta;
use Interpro\Extractor\Contracts\Items\COwnItem;
use Interpro\FileAggr\Items\FileItem;

class FileOwnField implements OwnFieldInterface
{
    private $item;
    private $field;
    private $name;
    private $owner;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Interpro\FileAggr\Items\FileItem
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param \Interpro\FileAggr\Items\FileItem $owner
     * @param \Interpro\Core\Contracts\Taxonomy\Fields\OwnField $field
     *
     * @return void
     */
    public function __construct(FileItem $owner, OwnFieldMeta $field)
    {
        $this->name = $field->getName();
        $this->owner = $owner;
        $this->field = $field;
    }

    /**
     * @return \Interpro\Extractor\Contracts\Items\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->item->getValue();
    }

    /**
     * @return \Interpro\Core\Contracts\Taxonomy\Fields\Field
     */
    public function getFieldMeta()
    {
        return $this->field;
    }

    /**
     * @param \Interpro\Extractor\Contracts\Items\COwnItem
     *
     * @return void
     */
    public function setItem(COwnItem $ownItem)
    {
        $this->item = $ownItem;
    }
}
