<?php

namespace Interpro\FileAggr\Settings\Collection;

use Interpro\Core\Contracts\Taxonomy\Fields\OwnField;
use Interpro\Core\Enum\OddEven;
use Interpro\Core\Iterator\FieldIterator;
use Interpro\Core\Iterator\OddEvenIterator;
use Interpro\FileAggr\Exception\FileAggrException;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet as FileSettingsSetInterface;

class FileSettingsSet implements FileSettingsSetInterface
{
    private $files;
    private $file_names;
    private $position;

    /**
     * @param array $files
     */
    public function __construct(
        array $files
    ){
        $this->files      = $files;
        $this->file_names = array_keys($files);
    }

    /**
     * @param \Interpro\Core\Contracts\Taxonomy\Fields\OwnField $field
     *
     * @return \Interpro\FileAggr\Contracts\Settings\FileSetting
     */
    public function getFile(OwnField $field)
    {
        $key = $field->getOwnerTypeName().'.'.$field->getName();

        if(array_key_exists($key, $this->files))
        {
            return $this->files[$key];
        }
        else
        {
            throw new FileAggrException('Картинка по имени: '.$key.' не найдена в коллекции!');
        }
    }

    function rewind()
    {
        $this->position = 0;
    }

    function current()
    {
        $name = $this->file_names[$this->position];
        return $this->files[$name];
    }

    function key()
    {
        return $this->file_names[$this->position];
    }

    function next()
    {
        ++$this->position;
    }

    function valid()
    {
        return isset($this->file_names[$this->position]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->file_names);
    }

    public function sortBy($path, $sort = 'ASC')
    {
        return new FieldIterator($this, $path, $sort);
    }

    public function odd()
    {
        return new OddEvenIterator($this->files, OddEven::ODD);
    }

    public function even()
    {
        return new OddEvenIterator($this->files, OddEven::EVEN);
    }

}
