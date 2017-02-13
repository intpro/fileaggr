<?php

namespace Interpro\FileAggr\Contracts\Settings\Collection;

use Interpro\Core\Contracts\Collection;
use Interpro\Core\Contracts\Taxonomy\Fields\OwnField;

interface FileSettingsSet extends Collection
{
    /**
     * @param \Interpro\Core\Contracts\Taxonomy\Fields\OwnField $field
     *
     * @return \Interpro\FileAggr\Contracts\Settings\FileSetting
     */
    public function getFile(OwnField $field);
}
