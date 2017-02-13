<?php

namespace Interpro\FileAggr\Contracts\Db;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Settings\FileSetting as FileSettingInterface;

interface FileAggrDbAgent
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     */
    public function ownerExist(ARef $aRef);

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     * @param array $attrs
     *
     * @return void
     */
    public function fileToDb(ARef $aRef, FileSettingInterface $fileSetting, $attrs = [], $init = false);

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     *
     * @return void
     */
    public function deleteFile(ARef $aRef, FileSettingInterface $fileSetting);

}
