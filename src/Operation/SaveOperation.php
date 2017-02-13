<?php

namespace Interpro\FileAggr\Operation;

use Illuminate\Support\Facades\File;
use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Settings\FileSetting;
use Interpro\FileAggr\Exception\FileAggrException;
use Interpro\FileAggr\Contracts\Operation\SaveOperation as SaveOperationInterface;

class SaveOperation extends Operation implements SaveOperationInterface
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     * @param array $user_attrs
     *
     * @return void
     */
    public function execute(ARef $aRef, FileSetting $fileSetting, array $user_attrs = [])
    {
        if(array_key_exists('update_flag', $user_attrs))
        {
            if($user_attrs['update_flag'] === 'false')
            {
                $update_flag = false;
            }
            else
            {
                $update_flag = (bool) $user_attrs['update_flag'];
            }
        }
        else
        {
            $update_flag = false;
        }

        $owner_name = $aRef->getType()->getName();
        $owner_id = $aRef->getId();
        $file_name = $fileSetting->getName();

        $this->checkOwner($aRef);

        $tmp_dir = $this->pathResolver->getTmpDir();

        if (!is_readable($tmp_dir))
        {
            throw new FileAggrException('Временная дирректория файлов ('.$tmp_dir.') не доступна для чтения!');
        }

        $files_dir = $this->pathResolver->getFileDir();

        if (!is_writable($files_dir))
        {
            throw new FileAggrException('Дирректория файлов ('.$files_dir.') не доступна для записи!');
        }

        $file_prefix = $this->getFilePrefix($owner_name, $owner_id, $file_name);

        $file_path = $files_dir.'/'.$file_prefix;

        $tmp_finded = false;
        $fileexist = false;

        $tmp_file_name = '';

        if($update_flag)
        {
            $tmp_path = $tmp_dir.'/'.$file_prefix;

            foreach (glob($tmp_path.'.*') as $file)
            {
                if(is_dir($file))
                {
                    continue;
                }

                $tmp_finded = true;
                $tmp_file_name = $file;
                break;
            }
        }
        else
        {
            //Удаление содержимого тэмпа
            $this->deleteAllFiles($aRef, $fileSetting, true);
        }

        if($tmp_finded)
        {
            //Удаление всех файлов файлов по пути без расширения, для очистки файлов отличающимся расширением от загруженной
            $this->deleteAllFiles($aRef, $fileSetting);

            $original_mime = File::mimeType($tmp_file_name);

            $original_ext = $this->getExtension($original_mime);

            if(!$original_ext)
            {
                $original_ext = File::extension($tmp_file_name);
            }

            $original_file_name = $file_path.'.'.$original_ext;

            File::move($tmp_file_name, $original_file_name);
            chmod($original_file_name, 0644);
            $fileexist = true;
        }
        else
        {
            foreach (glob($file_path.'.*') as $file)
            {
                if(is_dir($file))
                {
                    continue;
                }

                $fileexist = true;
                $original_mime = File::mimeType($file);
                $original_ext = $this->getExtension($original_mime);
                $original_file_name = $file_path.'.'.$original_ext;

                //Переименуем, если расширение не соответствует типу
                if($file !== $original_file_name)
                {
                    rename($file, $original_file_name);
                }

                break;
            }
        }

        if($fileexist)
        {
            $original_file_path = $this->pathResolver->getFilePath().'/'.$file_prefix.'.'.$original_ext;
        }
        else
        {
            $original_file_path = '';
        }

        $user_attrs['link'] = $original_file_path;

        $this->dbAgent->fileToDb($aRef, $fileSetting, $user_attrs);

        //Удаление содержимого тэмпа
        if($update_flag)
        {
            $this->deleteAllFiles($aRef, $fileSetting, true);
        }
    }

}
