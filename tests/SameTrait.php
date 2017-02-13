<?php

namespace Interpro\FileAggr\Test;

use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Taxonomy\Factory\TaxonomyFactory;
use Interpro\Core\Taxonomy\Manifests\ATypeManifest;
use Interpro\Core\Taxonomy\Manifests\BTypeManifest;
use Interpro\Core\Taxonomy\Manifests\CTypeManifest;
use Interpro\FileAggr\Settings\FileSettingsSetFactory;

trait SameTrait
{
    private function getSettings(Taxonomy $taxonomy)
    {
        $files_config = [
            'test_block.test_file2' => ['exts' => ['png'=>'Картинка png']]
        ];

        $fileSettingsFactory = new FileSettingsSetFactory($taxonomy, $this->pathResolver, $files_config);

        $fileSettngs = $fileSettingsFactory->create();//Все

        return $fileSettngs;
    }

    public function getTaxonomy()
    {
        $family = 'qs';
        $name = 'test_block';
        $owns = ['annotation'=>'string', 'test_file1'=>'file', 'test_file2'=>'file',];
        $manA1 = new ATypeManifest($family, $name, \Interpro\Core\Taxonomy\Enum\TypeRank::BLOCK, $owns, []);

        $family = 'scalar';
        $name = 'string';
        $manC1 = new \Interpro\Core\Taxonomy\Manifests\CTypeManifest($family, $name, [], []);

        $family = 'scalar';
        $name = 'bool';
        $manC2 = new \Interpro\Core\Taxonomy\Manifests\CTypeManifest($family, $name, [], []);

        $family = 'scalar';
        $name = 'int';
        $manC3 = new CTypeManifest($family, $name, [], []);

        $fileMan  = new BTypeManifest('fileaggr', 'file',
            ['name' => 'string'],
            []);

        $manifestsCollection = new \Interpro\Core\Taxonomy\Collections\ManifestsCollection();
        $manifestsCollection->addManifest($manA1);
        $manifestsCollection->addManifest($fileMan);
        $manifestsCollection->addManifest($manC1);
        $manifestsCollection->addManifest($manC2);
        $manifestsCollection->addManifest($manC3);

        $taxonomyFactory = new TaxonomyFactory();

        $taxonomy = $taxonomyFactory->createTaxonomy($manifestsCollection);

        return $taxonomy;
    }

}
