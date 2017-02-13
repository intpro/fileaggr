<?php

namespace Interpro\FileAggr\Contracts\Operation\Owner;

use Interpro\Core\Contracts\Ref\ARef;

interface OwnerInitOperationsCall
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     * @param array $user_attrs
     */
    public function execute(ARef $aRef, array $user_attrs = []);
}
