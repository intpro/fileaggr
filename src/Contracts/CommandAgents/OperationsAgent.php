<?php

namespace Interpro\FileAggr\Contracts\CommandAgents;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface OperationsAgent
{
    /**
     * @param string $owner_name
     * @param string $owner_id
     * @param string $file_name
     *
     * @return void
     */
    public function clean($owner_name, $owner_id, $file_name);

    /**
     * @param $owner_name
     * @param $owner_id
     * @param $file_name
     * @param UploadedFile $uploadedFile
     *
     * @return void
     */
    public function upload($owner_name, $owner_id, $file_name, UploadedFile $uploadedFile);
}
