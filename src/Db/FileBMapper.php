<?php

namespace Interpro\FileAggr\Db;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\Core\Contracts\Taxonomy\Fields\OwnField;
use Interpro\Core\Contracts\Taxonomy\Types\AType;
use Interpro\Core\Helpers;
use Interpro\Core\Taxonomy\Enum\TypeRank;
use Interpro\Extractor\Contracts\Db\BMapper;
use Interpro\Extractor\Contracts\Selection\SelectionUnit;
use Interpro\Extractor\Contracts\Selection\Tuner;
use Interpro\FileAggr\Collections\MapFileCollection;
use Interpro\FileAggr\Creation\CapGenerator;
use Interpro\FileAggr\Creation\FileItemFactory;

class FileBMapper implements BMapper
{
    private $itemFactory;
    private $fileQuerier;
    private $tuner;
    private $capGenerator;
    private $units = [];

    public function __construct(FileItemFactory $itemFactory, FileQuerier $fileQuerier, Tuner $tuner, CapGenerator $capGenerator)
    {
        $this->itemFactory  = $itemFactory;
        $this->fileQuerier = $fileQuerier;
        $this->tuner        = $tuner;
        $this->capGenerator = $capGenerator;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->units = [];
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return 'fileaggr';
    }

    private function createFile(OwnField $fileField, array $item_array)
    {
        $fileItem = $this->itemFactory->createFile($fileField, $item_array);

        return $fileItem;
    }

    /**
     * @param AType $ownerType
     * @param $files_result
     *
     * @return MapFileCollection
     */
    private function resultsToCollection(AType $ownerType, $files_result)
    {
        $collection = new MapFileCollection($this->capGenerator);

        foreach($files_result as $item_array)
        {
            $field_name = $item_array['name'];

            if($ownerType->fieldExist($field_name))
            {
                $fileField = $ownerType->getField($field_name);

                $fileItem = $this->createFile($fileField, $item_array);

                $ref = new \Interpro\Core\Ref\ARef($ownerType, $item_array['entity_id']);

                $collection->addItem($ref, $field_name, $fileItem);
            }
        }

        return $collection;
    }

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $ref
     * @param bool $asUnitMember
     *
     * @return \Interpro\Extractor\Contracts\Collections\MapBCollection
     */
    public function getByRef(ARef $ref, $asUnitMember = false)
    {
        $ownerType = $ref->getType();
        $type_name = $ownerType->getName();
        $typeRank = $ownerType->getRank();

        $key = $type_name.'_'.$ref->getId();

        if($typeRank === TypeRank::GROUP and $asUnitMember)
        {
            $selectionUnit = $this->tuner->getSelection($type_name, 'group');

            return $this->select($selectionUnit);
        }

        if(array_key_exists($key, $this->units))
        {
            return $this->units[$key];
        }

        $filesQuery = $this->fileQuerier->selectFilesByRef($ref);

        $files_result = Helpers::laravel_db_result_to_array($filesQuery->get());

        $collection = $this->resultsToCollection($ownerType, $files_result);

        $this->units[$key] = $collection;

        return $collection;
    }

    /**
     * @param \Interpro\Extractor\Contracts\Selection\SelectionUnit $selectionUnit
     *
     * @return \Interpro\Extractor\Contracts\Collections\MapBCollection
     */
    public function select(SelectionUnit $selectionUnit)
    {
        $ownerType = $selectionUnit->getType();

        $unit_number = $selectionUnit->getNumber();
        $key = 'unit_'.$unit_number;

        if(array_key_exists($key, $this->units))
        {
            return $this->units[$key];
        }

        //----------------------------------------------------------
        $filesQuery = $this->fileQuerier->selectFilesByUnit($selectionUnit);

        $files_result = Helpers::laravel_db_result_to_array($filesQuery->get());

        $collection = $this->resultsToCollection($ownerType, $files_result);

        $this->units[$key] = $collection;

        return $collection;
    }

}
