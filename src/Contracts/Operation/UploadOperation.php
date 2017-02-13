<?php

namespace Interpro\FileAggr\Contracts\Operation;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Settings\FileSetting;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadOperation
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     *
     * @return void
     */
    public function execute(ARef $aRef, FileSetting $fileSetting, UploadedFile $uploadedFile);
}
