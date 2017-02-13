<?php

namespace Interpro\FileAggr\Contracts\Settings;

interface PathResolver
{
    /**
     * @return string
     */
    public function getFileDir();

    /**
     * @return string
     */
    public function getTmpDir();

    /**
     * @return string
     */
    public function getIconDir();

    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @return string
     */
    public function getTmpPath();

    /**
     * @return string
     */
    public function getIconPath();

}
