<?php

namespace Interpro\FileAggr\Settings;

use Interpro\FileAggr\Contracts\Settings\PathResolver as PathResolverInterface;

class PathResolver implements PathResolverInterface
{
    private $file_dir;
    private $tmp_dir;
    private $icon_dir;

    private $file_path;
    private $tmp_path;
    private $icon_path;

    /**
     * @param array $dirs
     * @param array $paths
     * @param bool $test
     *
     * @return void
     */
    public function __construct(array $paths, $test = false)
    {
        $test = (bool) $test;
        $dir = public_path() . '/files'.($test ? '/test' : '');
        $path = '/files'.($test ? '/test' : '');

        $this->dir = $dir;
        $this->path = $path;

        $this->setAttr('file_dir',  'file', $dir,  '',    $paths);
        $this->setAttr('tmp_dir',   'tmp',  $dir,  'tmp', $paths);
        $this->setAttr('icon_dir',  'icon',  $dir, 'icons', $paths);

        $this->setAttr('file_path', 'file', $path,  '',    $paths);
        $this->setAttr('tmp_path',  'tmp',  $path,  'tmp', $paths);
        $this->setAttr('icon_path', 'icon',  $path, 'icons', $paths);
    }

    private function setAttr($name, $key, $dirpath, $def, array & $params)
    {
        $this->$name = $dirpath;

        if(array_key_exists($key, $params))
        {
            $continue_path = $params[$key];
        }
        else
        {
            $continue_path = $def;
        }

        if($continue_path)
        {
            $this->$name .= '/'.$continue_path;
        }
    }

    /**
     * @return string
     */
    public function getFileDir()
    {
        return $this->file_dir;
    }

    /**
     * @return string
     */
    public function getTmpDir()
    {
        return $this->tmp_dir;
    }

    /**
     * @return string
     */
    public function getIconDir()
    {
        return $this->icon_dir;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->file_path;
    }

    /**
     * @return string
     */
    public function getTmpPath()
    {
        return $this->tmp_path;
    }

    /**
     * @return string
     */
    public function getIconPath()
    {
        return $this->icon_path;
    }

}
