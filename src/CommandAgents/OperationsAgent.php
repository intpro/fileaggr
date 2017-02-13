<?php

namespace Interpro\FileAggr\CommandAgents;

use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Ref\ARef;
use Interpro\Core\Taxonomy\Enum\TypeMode;
use Interpro\FileAggr\Contracts\CommandAgents\OperationsAgent as OperationsAgentInterface;
use Interpro\FileAggr\Contracts\Operation\CleanOperation;
use Interpro\FileAggr\Contracts\Operation\UploadOperation;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet;
use Interpro\FileAggr\Exception\FileAggrException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OperationsAgent implements OperationsAgentInterface
{
    private $taxonomy;
    private $clean;
    private $upload;
    private $fileSettingsSet;

    public function __construct(Taxonomy $taxonomy,
                                FileSettingsSet $fileSettingsSet,
                                CleanOperation $clean,
                                UploadOperation $upload)
    {
        $this->taxonomy = $taxonomy;

        $this->clean   = $clean;
        $this->upload  = $upload;
        $this->fileSettingsSet = $fileSettingsSet;
    }

    private function ownerNameControl($owner_name)
    {
        if(!is_string($owner_name))
        {
            throw new FileAggrException('Название типа хозяина) должно быть задано строкой!');
        }
    }

    private function ownerIdControl($owner_id)
    {
        if(!is_int($owner_id))
        {
            throw new FileAggrException('Id типа хозяина должно быть задано целым числом!');
        }
    }

    private function fileNameControl($file_name)
    {
        if(!is_string($file_name))
        {
            throw new FileAggrException('Имя поля файла должно быть задано строкой!');
        }
    }

    private function cropNameControl($crop_name)
    {
        if(!is_string($crop_name))
        {
            throw new FileAggrException('Имя кропа файла должно быть задано строкой!');
        }
    }

    private function makeOwnerRef($owner_name, $owner_id)
    {
        $this->ownerNameControl($owner_name);
        $this->ownerIdControl($owner_id);

        $type = $this->taxonomy->getType($owner_name);

        $typeMode = $type->getMode();

        if($typeMode !== TypeMode::MODE_A)
        {
            throw new FileAggrException('Агент удаления может удалять только тип (A) уровня, передан тип:'.$type->getName().'('.$typeMode.')!');
        }

        $ref = new ARef($type, $owner_id);

        return $ref;
    }

    private function makeFileField($owner_name, $file_name)
    {
        $this->ownerNameControl($owner_name);
        $this->fileNameControl($file_name);

        $type = $this->taxonomy->getType($owner_name);

        $fileField = $type->getOwn($file_name);

        if($fileField->getFieldTypeName() !== 'file')
        {
            throw new FileAggrException('Тип поля хозяина '.$owner_name.' = '.$fileField->getName().' вместо ожидаемого file.');
        }

        return $fileField;
    }

    /**
     * @param string $owner_name
     * @param string $owner_id
     * @param string $file_name
     *
     * @return void
     */
    public function clean($owner_name, $owner_id, $file_name)
    {
        $aRef = $this->makeOwnerRef($owner_name, $owner_id);
        $fileField = $this->makeFileField($owner_name, $file_name);

        $fileSetting = $this->fileSettingsSet->getFile($fileField);

        $this->clean->execute($aRef, $fileSetting);
    }

    /**
     * @param $owner_name
     * @param $owner_id
     * @param $file_name
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     *
     * @return void
     */
    public function upload($owner_name, $owner_id, $file_name, UploadedFile $uploadedFile)
    {
        $aRef = $this->makeOwnerRef($owner_name, $owner_id);
        $fileField = $this->makeFileField($owner_name, $file_name);

        $fileSetting = $this->fileSettingsSet->getFile($fileField);

        $this->upload->execute($aRef, $fileSetting, $uploadedFile);
    }

}
