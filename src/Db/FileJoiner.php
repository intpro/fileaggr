<?php

namespace Interpro\FileAggr\Db;

use Illuminate\Support\Facades\DB;
use Interpro\Core\Contracts\Taxonomy\Fields\Field;
use Interpro\Core\Taxonomy\Enum\TypeMode;
use Interpro\Extractor\Contracts\Db\Joiner;
use Interpro\Extractor\Db\QueryBuilder;
use Interpro\FileAggr\Exception\FileAggrException;

class FileJoiner implements Joiner
{
    /**
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @param \Interpro\Core\Contracts\Taxonomy\Fields\Field $field
     * @param array $join_array
     *
     * @return \Interpro\Extractor\Db\QueryBuilder
     */
    public function joinByField(Field $field, $join_array)
    {
        $fieldType = $field->getFieldType();
        $type_name = $fieldType->getName();
        $field_name = $field->getName();
        $mode = $fieldType->getMode();

        $file_table_fields = ['id', 'name', 'entity_name', 'entity_id', 'link', 'title'];

        if($type_name !== 'file' or $mode !== TypeMode::MODE_B)
        {
            throw new FileAggrException('Соединитель предназначен для соединения с основным запросом поля типа files(B), передано: '.$type_name.'('.$mode.')!');
        }

        $join_q = new QueryBuilder(DB::table('files'));

        $join_q->addSelect('files.entity_name');
        $join_q->addSelect('files.entity_id');
        $join_q->whereRaw('files.name = "'.$field_name.'"');

        //Если в продолжения пути нет, то $field_name и есть нужное поле
        foreach($join_array['sub_levels'] as $levelx_field_name => $sub_array)
        {
            if(in_array($levelx_field_name, $file_table_fields))
            {
                $join_q->addSelect('files.'.$levelx_field_name.' as '.$sub_array['full_field_names'][0]);//Законцовка - в массиве только одно поле x_..x_id
            }
            else
            {
                throw new FileAggrException('Соединение в целях сортировки или отбора возможно только по следующим полям сущности файла: '.implode(',', $file_table_fields).'!');
            }
        }

        return $join_q;
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return 'fileaggr';
    }

}
