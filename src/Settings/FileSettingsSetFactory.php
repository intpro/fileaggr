<?php

namespace Interpro\FileAggr\Settings;

use Interpro\FileAggr\Collections\ExtsCollection;
use Interpro\FileAggr\Contracts\Settings\FileSettingsSetFactory as FileSettingsSetFactoryInterface;

use Interpro\FileAggr\Contracts\Settings\PathResolver as PathResolverInterface;
use Interpro\FileAggr\Settings\Collection\FileSettingsSet;
use Interpro\Core\Contracts\Taxonomy\Taxonomy as TaxonomyInterface;

class FileSettingsSetFactory implements FileSettingsSetFactoryInterface
{
    private $files_config;
    private $taxonomy;
    private $file_settings;
    private $pathResolver;
    private $extAll;

    /**
     * @param \Interpro\Core\Contracts\Taxonomy\Taxonomy $taxonomy
     * @param \Interpro\FileAggr\Contracts\Settings\PathResolver $pathResolver
     * @param array $files_config
     *
     * @return void
     */
    public function __construct(TaxonomyInterface $taxonomy, PathResolverInterface $pathResolver, array $files_config)
    {
        $this->files_config  = $files_config;
        $this->taxonomy      = $taxonomy;
        $this->pathResolver  = $pathResolver;
        $this->file_settings = [];
        $this->extAll = new Extension('all', 'все файлы', $this->pathResolver);
    }

    private function createFileSetting($owner_type, $field_name)
    {
        $file_key = $owner_type.'.'.$field_name;

        if(array_key_exists($file_key, $this->file_settings))
        {
            return $this->file_settings[$file_key];
        }

        $exts = [];

        if(array_key_exists($file_key, $this->files_config))
        {
            $attrs = $this->files_config[$file_key];

            if(array_key_exists('exts', $attrs))
            {
                $exts = $attrs['exts'];
            }
        }

        $extsCollection = new ExtsCollection();

        if(is_array($exts))
        {
            foreach($exts as $ext_name => $ext_descr)
            {
                $newExt = new Extension($ext_name, $ext_descr, $this->pathResolver);
                $extsCollection->addExt($newExt);
            }
        }

        if($extsCollection->count() === 0)
        {
            $extsCollection->addExt($this->extAll);
        }

        $fileSetting = new FileSetting($owner_type, $field_name, $extsCollection);

        //Закэшируем, чтобы экземпляры не повторялись
        $this->file_settings[$file_key] = $fileSetting;

        return $fileSetting;
    }

    /**
     * @param $owner_name
     *
     * @return \Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet
     */
    public function create($owner_name = 'all')
    {
        //Пытаемся получить из $files_config, если нет, то создаем без конфига с одним original линком
        //Получаем массив использования типа файла
        $fileType = $this->taxonomy->getType('file');

        $using = $fileType->getUsing();

        $fileSettings = [];

        if($owner_name === 'all')
        {
            foreach($using as $owner_type_name => $ownerType)
            {
                $fields = $ownerType->getOwns()->getTyped('file');

                foreach($fields as $field_name => $field)
                {
                    $fileSetting = $this->createFileSetting($owner_type_name, $field_name);
                    $fileSettings[$owner_type_name.'.'.$field_name] = $fileSetting;
                }
            }
        }
        else
        {
            //Проверим тип хозяина на существование
            $ownerType = $this->taxonomy->getType($owner_name);

            $owner_type_name = $ownerType->getName();

            $fields = $ownerType->getOwns()->getTyped('file');

            foreach($fields as $field_name => $field)
            {
                $fileSetting = $this->createFileSetting($owner_type_name, $field_name);
                $fileSettings[$owner_type_name.'.'.$field_name] = $fileSetting;
            }
        }

        $fileSettingsSet = new FileSettingsSet($fileSettings);

        return $fileSettingsSet;
    }
}
