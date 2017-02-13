<?php

namespace Interpro\FileAggr\Contracts\Settings;

interface FileSetting
{
    /**
     * @return string
     */
    public function getEntityName();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return \Interpro\FileAggr\Collections\ExtsCollection
     */
    public function getExts();

    /**
     * @return \Interpro\FileAggr\Settings\Extension
     */
    public function getExt();

    /**
     * @return bool
     */
    public function extAvailable($ext_name);

}
