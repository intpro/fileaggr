<?php

namespace Interpro\FileAggr\Db;

use Illuminate\Support\Facades\DB;
use Interpro\Core\Contracts\Ref\ARef;
use Interpro\Extractor\Contracts\Selection\SelectionUnit;
use Interpro\Extractor\Db\QueryBuilder;

class FileQuerier
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $ref
     *
     * @return \Interpro\Extractor\Db\QueryBuilder
     */
    private function selectByRef(ARef $ref)
    {
        $type  = $ref->getType();
        $type_name = $type->getName();
        $id = $ref->getId();

        $query = new QueryBuilder(DB::table('files'));
        $query->where('files.entity_name', '=', $type_name);

        if($id > 0)
        {
            $query->where('files.entity_id', '=', $id);
        }

        return $query;
    }

    /**
     * @param SelectionUnit $selectionUnit
     *
     * @return \Interpro\Extractor\Db\QueryBuilder
     */
    public function selectByUnit(SelectionUnit $selectionUnit)
    {
        $type  = $selectionUnit->getType();
        $entity_name    = $type->getName();

        $query = new QueryBuilder(DB::table('files'));
        $query->where('files.entity_name', '=', $entity_name);

        if($selectionUnit->closeToIdSet())
        {
            $query->whereIn('files.entity_id', $selectionUnit->getIdSet());
        }

        return $query;
    }

    /**
     * @param SelectionUnit $selectionUnit
     *
     * @return \Interpro\Extractor\Db\QueryBuilder
     */
    public function selectFilesByUnit(SelectionUnit $selectionUnit)
    {
        return $this->selectByUnit($selectionUnit);
    }

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $ref
     *
     * @return \Interpro\Extractor\Db\QueryBuilder
     */
    public function selectFilesByRef(ARef $ref)
    {
        return $this->selectByRef($ref);
    }

}
