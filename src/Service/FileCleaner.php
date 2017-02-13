<?php

namespace Interpro\FileAggr\Service;

use Interpro\FileAggr\Contracts\Settings\PathResolver;
use Interpro\FileAggr\Model\File;
use Interpro\Service\Contracts\Cleaner as CleanerInterface;
use Interpro\Service\Enum\Artefact;

class FileCleaner implements CleanerInterface
{
    private $pathResolver;
    private $consoleOutput;

    public function __construct(PathResolver $pathResolver)
    {
        $this->pathResolver = $pathResolver;
        $this->consoleOutput = new \Symfony\Component\Console\Output\ConsoleOutput();
    }

    /**
     * @param callable $action
     *
     * @return bool
     */
    private function strategy(callable $action)
    {
        $file_links = [];

        $file_links_finded = [];

        $originals = File::all();

        foreach($originals as $model)
        {
            $file_links[] = public_path().$model->link;
        }

        $file_dir = $this->pathResolver->getFileDir();

        foreach (glob($file_dir.'/*.*') as $file)
        {
            if(is_dir($file))
            {
                continue;
            }

            $file_links_finded[] = $file;
        }

        $report = false;

        $file_diff  = array_diff($file_links_finded, $file_links);

        if((count($file_diff)) > 0)
        {
            $report = true;
        }

        foreach($file_diff as $file_link)
        {
            $action(1, $file_link);
        }

        return $report;
    }

    /**
     * @return bool
     */
    public function inspect()
    {
        $action = function($flag, $link)
        {
            if($flag === 1)
            {
                $this->consoleOutput->writeln('FileAggr: обнаружен файл '.$link.' в папке файлов, ссылки на который отсутствуют в базе данных.');
            }
            elseif($flag === 2)
            {
                $this->consoleOutput->writeln('FileAggr: обнаружен файл '.$link.' в папке ресайзов, ссылки на который отсутствуют в базе данных.');
            }
            elseif($flag === 3)
            {
                $this->consoleOutput->writeln('FileAggr: обнаружен файл '.$link.' в папке кропов, ссылки на который отсутствуют в базе данных.');
            }
            else
            {
                return;
            }
        };

        $report = $this->strategy($action);

        return $report;
    }

    /**
     * @return void
     */
    public function clean()
    {
        $action = function($flag, $link)
        {
            if($flag === 1)
            {
                unlink($link);
                $this->consoleOutput->writeln('FileAggr: удалён файл '.$link.' в папке файлов, ссылки на который отсутствуют в базе данных.');
            }
            elseif($flag === 2)
            {
                unlink($link);
                $this->consoleOutput->writeln('FileAggr: удалён файл '.$link.' в папке ресайзов, ссылки на который отсутствуют в базе данных.');
            }
            elseif($flag === 3)
            {
                unlink($link);
                $this->consoleOutput->writeln('FileAggr: удалён файл '.$link.' в папке кропов, ссылки на который отсутствуют в базе данных.');
            }
            else
            {
                return;
            }
        };

        $this->strategy($action);
    }

    /**
     * @return string
     */
    public function getArtefact()
    {
        return Artefact::FILE;
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return 'fileaggr';
    }
}
