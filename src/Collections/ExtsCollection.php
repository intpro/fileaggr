<?php

namespace Interpro\FileAggr\Collections;

use Interpro\Core\Taxonomy\Collections\NamedCollection;
use Interpro\FileAggr\Exception\FileAggrException;
use Interpro\FileAggr\Settings\Extension;

class ExtsCollection extends NamedCollection
{
    /**
     * @param string $name
     *
     * @return \Interpro\FileAggr\Settings\Extension
     */
    public function getExt($name)
    {
        return $this->getByName($name);
    }

    /**
     * @param string $separator
     * @return string
     */
    public function implode($separator = ',')
    {
        return implode($separator, $this->item_names);
    }

    protected function notFoundAction($name)
    {
        throw new FileAggrException('Не найден тип по имени '.$name.'!');
    }

    /**
     * @param \Interpro\FileAggr\Settings\Extension
     */
    public function addExt(Extension $extension)
    {
        $this->addByName($extension);
    }
}
