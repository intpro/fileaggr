<?php

namespace Interpro\FileAggr;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Interpro\Core\Contracts\Mediator\SyncMediator;
use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Taxonomy\Manifests\BTypeManifest;
use Interpro\Extractor\Contracts\Creation\CItemBuilder;
use Interpro\Extractor\Contracts\Creation\CollectionFactory;
use Interpro\Extractor\Contracts\Db\JoinMediator;
use Interpro\Extractor\Contracts\Db\MappersMediator;
use Interpro\Extractor\Contracts\Selection\Tuner;
use Interpro\FileAggr\Contracts\Operation\InitOperation;
use Interpro\FileAggr\Contracts\Operation\Owner\OwnerDeleteOperationsCall;
use Interpro\FileAggr\Contracts\Operation\SaveOperation;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet;
use Interpro\FileAggr\Contracts\Settings\PathResolver;
use Interpro\FileAggr\Creation\CapGenerator;
use Interpro\FileAggr\Creation\FileItemFactory;
use Interpro\FileAggr\Db\FileBMapper;
use Interpro\FileAggr\Db\FileJoiner;
use Interpro\FileAggr\Db\FileQuerier;
use Interpro\Core\Contracts\Mediator\DestructMediator;
use Interpro\Core\Contracts\Mediator\InitMediator;
use Interpro\Core\Contracts\Mediator\UpdateMediator;
use Interpro\FileAggr\Exception\FileAggrException;
use Interpro\FileAggr\Executors\Destructor;
use Interpro\FileAggr\Executors\Initializer;
use Interpro\FileAggr\Executors\Synchronizer;
use Interpro\FileAggr\Executors\UpdateExecutor;
use Interpro\FileAggr\Service\DbCleaner;
use Interpro\FileAggr\Service\FileCleaner;
use Interpro\Service\Contracts\CleanMediator;

class FileAggrSecondServiceProvider extends ServiceProvider {

    /**
     * @return void
     */
    public function boot(Dispatcher $dispatcher,
                         Taxonomy $taxonomy,
                         InitMediator $initMediator,
                         SyncMediator $syncMediator,
                         UpdateMediator $updateMediator,
                         DestructMediator $destructMediator,
                         MappersMediator $mappersMediator,
                         JoinMediator $joinMediator,
                         CollectionFactory $collectionFactory,
                         CItemBuilder $cItemBuilder,
                         InitOperation $initOperation,
                         SaveOperation $saveOperation,
                         OwnerDeleteOperationsCall $deleteOperationsCall,
                         FileSettingsSet $settingsSet,
                         Tuner $tuner,
                         CleanMediator $cleanMediator,
                         PathResolver $pathResolver)
    {
        //Log::info('Загрузка FileAggrSecondServiceProvider');

        $initializer = new Initializer($initOperation, $settingsSet);
        $initMediator->registerBInitializer($initializer);

        $synchronizer = new Synchronizer($initOperation, $settingsSet);
        $syncMediator->registerOwnSynchronizer($synchronizer);

        $updateExecutor = new UpdateExecutor($saveOperation, $settingsSet);
        $updateMediator->registerBUpdateExecutor($updateExecutor);

        $destructor = new Destructor($deleteOperationsCall);
        $destructMediator->registerBDestructor($destructor);

        //Для Extractor'a
        $capGenerator = new CapGenerator($settingsSet);
        $itemFactory = new FileItemFactory($collectionFactory, $cItemBuilder, $settingsSet, $capGenerator);
        $capGenerator->setFactory($itemFactory);//Взаимная зависимость

        $fileQuerier = new FileQuerier();
        $mapper = new FileBMapper($itemFactory, $fileQuerier, $tuner, $capGenerator);
        $mappersMediator->registerBMapper($mapper);

        $joiner = new FileJoiner();
        $joinMediator->registerJoiner($joiner);

        //Для сервиса
        $cleanerdb = new DbCleaner($taxonomy, $settingsSet);
        $cleanMediator->registerCleaner($cleanerdb);

        //Для сервиса
        $cleanerfile = new FileCleaner($pathResolver);
        $cleanMediator->registerCleaner($cleanerfile);
    }

    /**
     * @return void
     */
    public function register()
    {
        //Log::info('Регистрация FileAggrSecondServiceProvider');

        $forecastList = App::make('Interpro\Core\Contracts\Taxonomy\TypesForecastList');
        $typeRegistrator = App::make('Interpro\Core\Contracts\Taxonomy\TypeRegistrator');

        $cNames = $forecastList->getCTypeNames();

        $message = 'Ошибка регистрации пакета fileaggr.';
        $err = false;

        if(!in_array('string', $cNames))
        {
            $err = true;
            $message .= PHP_EOL.'Не зарегестрировано имя типа string.';
        }

        if(!in_array('int', $cNames))
        {
            $err = true;
            $message .= PHP_EOL.'Не зарегестрировано имя типа int.';
        }

        if(!in_array('bool', $cNames))
        {
            $err = true;
            $message .= PHP_EOL.'Не зарегестрировано имя типа bool.';
        }

        if($err)
        {
            $message .= PHP_EOL.'Интерпретация предопределенных полей агрегатных типа file не возможна!';
            throw new FileAggrException($message);
        }

        //-----------------------------------------------------------

        $fileMan  = new BTypeManifest('fileaggr', 'file',
            [
                'name' => 'string',
                'title' => 'string',
                'link' => 'string'
            ],
            []);

        $typeRegistrator->registerType($fileMan);
    }

}
