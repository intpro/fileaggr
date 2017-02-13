<?php

namespace Interpro\FileAggr\Creation;

use Interpro\Core\Contracts\Taxonomy\Fields\OwnField;
use Interpro\Extractor\Contracts\Creation\CItemBuilder;
use Interpro\Extractor\Contracts\Creation\CollectionFactory;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet;
use Interpro\FileAggr\Exception\FileAggrException;
use Interpro\FileAggr\Fields\FileOwnField;
use Interpro\FileAggr\Items\FileItem;

/**
 * Значения ссылок проставляются вне этой фабрики
 * Class FileItemFactory
 *
 * @package Interpro\FileAggr\Creation
 */
class FileItemFactory
{
    private $collectionFactory;
    private $cItemBuilder;
    private $settingsSet;
    private $defs;

    public function __construct(CollectionFactory $collectionFactory, CItemBuilder $cItemBuilder, FileSettingsSet $settingsSet, CapGenerator $capGenerator)
    {
        $this->collectionFactory = $collectionFactory;
        $this->cItemBuilder = $cItemBuilder;
        $this->settingsSet = $settingsSet;
        $this->capGenerator = $capGenerator;

        $this->defs = [
            'integer' => 0,
            'string' => '',
            'boolean' => false
        ];
    }

    /**
     * @param FileItem $owner
     * @param OwnField $ownMeta
     * @param mixed $value
     *
     * @return \Interpro\FileAggr\Fields\FileOwnField
     */
    private function createLocalCField(FileItem $owner, OwnField $ownMeta, $value)
    {
        $fieldType = $ownMeta->getFieldType();

        $scalarItem = $this->cItemBuilder->create($fieldType, $value);

        $newField = new FileOwnField($owner, $ownMeta);
        $newField->setItem($scalarItem);

        return $newField;
    }

    private function checkAndGetData(array $data, $name, $type_name)
    {
        if(!is_string($name))
        {
            throw new FileAggrException('Имя поля должно быть задано строкой, передано: '.gettype($name).'!');
        }

        if(!array_key_exists($type_name, $this->defs))
        {
            throw new FileAggrException('Собственные поля файла могут быть типа '.implode(',', $this->defs).', передано '.$type_name.'!');
        }

        if(array_key_exists($name, $data))
        {
            $value = $data[$name];
        }
        else
        {
            $value = $this->defs[$type_name];
        }

        if($type_name === 'integer')
        {
            $value = (int) $value;
        }
        elseif($type_name === 'string')
        {
            $value = (string) $value;
        }
        elseif($type_name === 'boolean')
        {
            $value = (bool) $value;
        }
        elseif(gettype($value) !== $type_name)
        {
            throw new FileAggrException('Значение не соответствует заявленному типу: '.$value.'('.$type_name.')!');
        }

        return $value;
    }

    /**
     * @param \Interpro\Core\Contracts\Taxonomy\Fields\OwnField $field
     * @param array $data
     * @param bool $cap
     *
     * @return \Interpro\FileAggr\Items\FileItem
     */
    public function createFile(OwnField $field, array $data, $cap = false)
    {
        $fileType = $field->getFieldType();

        if($fileType->getName() !== 'file')
        {
            throw new FileAggrException('Попытка создания сущности файла для поля не файла ('.$fileType->getName().')!');
        }

        $fields  = $this->collectionFactory->createFieldsCollection();
        $owns    = $this->collectionFactory->createOwnsCollection();
        $refs    = $this->collectionFactory->createRefsCollection();

        $setting = $this->settingsSet->getFile($field);

        $fileItem = new FileItem($field, $fields, $owns, $refs, $setting, $cap);

        $metaName = $fileType->getOwn('name');
        $metaLink = $fileType->getOwn('link');
        $metaTitle = $fileType->getOwn('title');

        $fieldName = $this->createLocalCField($fileItem, $metaName, $this->checkAndGetData($data, 'name', 'string'));
        $fieldLink = $this->createLocalCField($fileItem, $metaLink, $this->checkAndGetData($data, 'link', 'string'));
        $fieldTitle = $this->createLocalCField($fileItem, $metaTitle, $this->checkAndGetData($data, 'title', 'string'));

        //Собственные поля
        $fileItem->setOwn($fieldName);  $fileItem->setField($fieldName);
        $fileItem->setOwn($fieldLink);  $fileItem->setField($fieldLink);
        $fileItem->setOwn($fieldTitle);  $fileItem->setField($fieldTitle);

        return $fileItem;
    }

}
