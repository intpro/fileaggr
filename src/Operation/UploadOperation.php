<?php

namespace Interpro\FileAggr\Operation;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Settings\FileSetting;
use Interpro\FileAggr\Exception\FileAggrException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Interpro\FileAggr\Contracts\Operation\UploadOperation as UploadOperationInterface;

class UploadOperation extends Operation implements UploadOperationInterface
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     *
     * @return void
     */
    public function execute(ARef $aRef, FileSetting $fileSetting, UploadedFile $uploadedFile)
    {
        $owner_name = $aRef->getType()->getName();
        $owner_id = $aRef->getId();
        $file_name = $fileSetting->getName();

        $this->checkOwner($aRef);

        $tmp_dir = $this->pathResolver->getTmpDir();

        if (!is_writable($tmp_dir))
        {
            throw new FileAggrException('Временная дирректория файлов ('.$tmp_dir.') не доступна для записи!');
        }

        $ext = $uploadedFile->guessClientExtension();

        if(!$fileSetting->extAvailable($ext))
        {
            throw new FileAggrException('Тип файла может быть только '.$fileSetting->getExts()->implode(',').' передан тип '.$ext.'!');
        }

        $file_name_ext = $this->getFilePrefix($owner_name, $owner_id, $file_name).'.'.$ext;

        $this->deleteAllFiles($aRef, $fileSetting, true);

        $uploadedFile->move(
            $tmp_dir,
            $file_name_ext
        );

        $tmp_path = $tmp_dir.'/'.$file_name_ext;

        chmod($tmp_path, 0644);
    }
}
