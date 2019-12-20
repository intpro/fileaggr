<?php

namespace Interpro\FileAggr;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Interpro\FileAggr\Settings\FileSettingsSetFactory;
use Interpro\FileAggr\Settings\PathResolver;

class FileAggrUseServiceProvider extends ServiceProvider {

    /**
     * @return void
     */
    public function boot(Dispatcher $dispatcher)
    {
        //Log::info('Загрузка FileAggrUseServiceProvider');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Log::info('Регистрация FileAggrUseServiceProvider');

        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Db\FileAggrDbAgent',
            'Interpro\FileAggr\Db\FileAggrDbAgent'
        );

        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Settings\PathResolver',
            function($app)
            {
                $test = $app->runningUnitTests();
                return new PathResolver(config('interpro.fileaggr.paths', []), $test);
            }
        );

        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Settings\FileSettingsSetFactory',
            function($app)
            {
                $taxonomy = $app->make('Interpro\Core\Contracts\Taxonomy\Taxonomy');
                $pathResolver = $app->make('Interpro\FileAggr\Contracts\Settings\PathResolver');

                return new FileSettingsSetFactory($taxonomy, $pathResolver, config('interpro.files', []));
            }
        );

        //----------------------------------------------------------------
        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Operation\CleanOperation',
            'Interpro\FileAggr\Operation\CleanOperation'
        );
        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Operation\DeleteOperation',
            'Interpro\FileAggr\Operation\DeleteOperation'
        );
        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Operation\InitOperation',
            'Interpro\FileAggr\Operation\InitOperation'
        );
        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Operation\SaveOperation',
            'Interpro\FileAggr\Operation\SaveOperation'
        );
        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Operation\UploadOperation',
            'Interpro\FileAggr\Operation\UploadOperation'
        );
        //---------------------------------------------------------
        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Operation\Owner\OwnerDeleteOperationsCall',
            'Interpro\FileAggr\Operation\Owner\OwnerDeleteOperationsCall'
        );
        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Operation\Owner\OwnerInitOperationsCall',
            'Interpro\FileAggr\Operation\Owner\OwnerInitOperationsCall'
        );
        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Operation\Owner\OwnerSaveOperationsCall',
            'Interpro\FileAggr\Operation\Owner\OwnerSaveOperationsCall'
        );
        //---------------------------------------------------------

        $this->app->singleton(
            'Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet',
            function($app)
            {
                $factory = $app->make('Interpro\FileAggr\Contracts\Settings\FileSettingsSetFactory');
                return $factory->create();
            }
        );

        $this->app->singleton(
            'Interpro\FileAggr\Contracts\CommandAgents\OperationsAgent',
            'Interpro\FileAggr\CommandAgents\OperationsAgent'
        );

        $oa = App::make('Interpro\FileAggr\Contracts\CommandAgents\OperationsAgent');

        $this->app->make('Interpro\FileAggr\Http\FileOperationController');

        include __DIR__ . '/Http/routes.php';
    }

}
