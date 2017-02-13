<?php

namespace Interpro\FileAggr;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class FileAggrFirstServiceProvider extends ServiceProvider {

    /**
     * @return void
     */
    public function boot(Dispatcher $dispatcher)
    {
        //Log::info('Загрузка FileAggrFirstServiceProvider');

        //-----------------------------------------------------------
        $this->publishes([__DIR__.'/config/files.php' => config_path('interpro/files.php')]);
        $this->publishes([__DIR__.'/config/fileaggr.php' => config_path('interpro/fileaggr.php')]);

        $this->publishes([
            __DIR__.'/migrations' => $this->app->databasePath().'/migrations'
        ], 'migrations');

        //Создание основных папок -----------------------------------
        if(!File::isDirectory(public_path('files')))
        {
            File::makeDirectory(public_path('files'));
        }

        if(!File::isDirectory(public_path('files/icons')))
        {
            File::makeDirectory(public_path('files/icons'));
        }

        //Создание папок временного хранения для обеспечения процесса выборка файлов в админ. панели -----
        if(!File::isDirectory(public_path('files/tmp')))
        {
            File::makeDirectory(public_path('files/tmp'));
        }

        //---------------------------------------для тэстов-------------

        if(!File::isDirectory(public_path('files/test')))
        {
            File::makeDirectory(public_path('files/test'));
        }

        if(!File::isDirectory(public_path('files/test/icons')))
        {
            File::makeDirectory(public_path('files/test/icons'));
        }

        //Создание папок временного хранения для обеспечения процесса выборка файлов из панели
        if(!File::isDirectory(public_path('files/test/tmp')))
        {
            File::makeDirectory(public_path('files/test/tmp'));
        }

        $this->publishes([
            __DIR__.'/icons' => public_path('files/icons')
        ], 'icons');

        $this->publishes([
            __DIR__.'/icons' => public_path('files/test/icons')
        ], 'icons');

    }

    /**
     * @return void
     */
    public function register()
    {
        //Log::info('Регистрация FileAggrFirstServiceProvider');

        //Регистрируем имена, для интерпретации типов при загрузке
        $forecastList = $this->app->make('Interpro\Core\Contracts\Taxonomy\TypesForecastList');

        $forecastList->registerBTypeName('file');
    }

}
