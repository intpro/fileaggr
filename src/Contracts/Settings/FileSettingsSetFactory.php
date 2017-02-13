<?php

namespace Interpro\FileAggr\Contracts\Settings;

interface FileSettingsSetFactory
{
    /**
     * @param $owner_name
     *
     * @return \Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet
     */
    public function create($owner_name = 'all');
}
