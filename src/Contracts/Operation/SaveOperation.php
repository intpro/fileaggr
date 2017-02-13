<?php

namespace Interpro\FileAggr\Contracts\Operation;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Settings\FileSetting;

interface SaveOperation
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     * @param array $user_attrs
     */
    public function execute(ARef $aRef, FileSetting $fileSetting, array $user_attrs = []);
}
