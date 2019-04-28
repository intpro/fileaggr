<?php

namespace Interpro\FileAggr\Test;

use Illuminate\Support\Facades\File;
use Interpro\Core\Ref\ARef;
use Interpro\FileAggr\Db\TestFileAggrDbAgent;
use Interpro\FileAggr\Operation\CleanOperation;
use Interpro\FileAggr\Operation\DeleteOperation;
use Interpro\FileAggr\Operation\InitOperation;
use Interpro\FileAggr\Operation\SaveOperation;
use Interpro\FileAggr\Operation\UploadOperation;
use Interpro\FileAggr\Settings\PathResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Foundation\Testing\TestCase;

class OperationsTest extends TestCase
{
    private $fileSettngs;
    private $taxonomy;
    private $pathResolver;
    private $dbAgent;

    use SameTrait;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp(): void
    {
        parent::setUp();

        //Создание основных папок -------------------------------------------------------------------------
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

        $this->taxonomy = $this->getTaxonomy();
        $this->pathResolver = new PathResolver([], true);
        $this->fileSettngs = $this->getSettings($this->taxonomy);
        $this->dbAgent = new TestFileAggrDbAgent();
    }

    public function tearDown(): void
    {
        $file_dir = $this->pathResolver->getFileDir();

        foreach (glob($file_dir.'/test_block*.*') as $file)
        {
            if(is_dir($file))
            {
                continue;
            }

            unlink($file);
        }
    }

    public function testFileLife()
    {
        $this->initOperation();
        $this->uploadOperation();
        $this->saveOperation();
        $this->deleteOperation();
    }

    public function initOperation()
    {
        $this->dbAgent->setFiles(
            [
                [
                    'id' => 1,
                    'name' => 'test_file1',
                    'entity_name' => 'test_block',
                    'entity_id' => 0,
                    'link' => '',
                    'title' => ''
                ],
                [
                    'id' => 2,
                    'name' => 'test_file2',
                    'entity_name' => 'test_block',
                    'entity_id' => 0,
                    'link' => '',
                    'title' => ''
                ],
            ]
        );

        $init = new InitOperation($this->pathResolver, $this->taxonomy, $this->dbAgent);

        $blockType = $this->taxonomy->getType('test_block');
        $testBlockFields = $blockType->getOwns()->getTyped('file');
        $file1Field = $testBlockFields->getField('test_file1');
        $file2Field = $testBlockFields->getField('test_file2');
        $file1Setting = $this->fileSettngs->getFile($file1Field);
        $file2Setting = $this->fileSettngs->getFile($file2Field);
        $aRef = new ARef($blockType, 0);

        $init->execute($aRef, $file1Setting);
        $init->execute($aRef, $file2Setting);

        $all_icon_jpg_path1 = $file1Setting->getExt()->icon;
        $all_icon_jpg_path2 = $file2Setting->getExt()->icon;

        $this->assertEquals('/files/test/icons/all_icon.jpg', $all_icon_jpg_path1);
        $this->assertEquals('/files/test/icons/png_icon.jpg', $all_icon_jpg_path2);
    }

    /**
     * @depends testInitOperation
     */
    public function uploadOperation()
    {
        $tmp_file_path = sys_get_temp_dir().'/test_file_bla.txt';
        $fd = fopen($tmp_file_path, 'a');
        fputs($fd, 'this is test file'.'\n');
        fclose($fd);

        //Создать симфони аплоад
        $uploadedFile = new UploadedFile(
            $tmp_file_path,
            'test_file_bla.txt',
            'text/plain',
            filesize($tmp_file_path),
            null, true
        );

        $upload = new UploadOperation($this->pathResolver, $this->taxonomy, $this->dbAgent);

        //Выполнить операцию
        $blockType = $this->taxonomy->getType('test_block');
        $testBlockFields = $blockType->getOwns()->getTyped('file');
        $testFileField = $testBlockFields->getField('test_file1');
        $testFileSetting = $this->fileSettngs->getFile($testFileField);
        $aRef = new ARef($blockType, 0);

        $upload->execute($aRef, $testFileSetting, $uploadedFile);

        $file_name = 'test_block_0_test_file1.txt';
        $file_path = $this->pathResolver->getTmpDir().'/'.$file_name;

        //Проверить наличие файлов
        $this->assertFileExists($file_path, 'upload');
    }

    /**
     * @expectedException \Interpro\FileAggr\Exception\FileAggrException
     */
    public function testWrongUpload()
    {
        $tmp_file_path = sys_get_temp_dir().'/test_file_bla_2.bla';
        $fd = fopen($tmp_file_path, 'a');
        fputs($fd, 'this is test file'.'\n');
        fclose($fd);

        //Создать симфони аплоад
        $uploadedFile = new UploadedFile(
            $tmp_file_path,
            'test_file_bla_2.bla',
            'text/plain',
            filesize($tmp_file_path),
            null, true
        );

        $upload = new UploadOperation($this->pathResolver, $this->taxonomy, $this->dbAgent);

        //Выполнить операцию
        $blockType = $this->taxonomy->getType('test_block');
        $testBlockFields = $blockType->getOwns()->getTyped('file');
        $testFileField = $testBlockFields->getField('test_file2');
        $testFileSetting = $this->fileSettngs->getFile($testFileField);
        $aRef = new ARef($blockType, 0);

        $upload->execute($aRef, $testFileSetting, $uploadedFile);
    }

    /**
     * @depends testUploadOperation
     */
    public function saveOperation()
    {
        $save = new SaveOperation($this->pathResolver, $this->taxonomy, $this->dbAgent);

        $blockType = $this->taxonomy->getType('test_block');
        $testBlockFields = $blockType->getOwns()->getTyped('file');
        $testFileField = $testBlockFields->getField('test_file1');
        $testFileSetting = $this->fileSettngs->getFile($testFileField);
        $aRef = new ARef($blockType, 0);

        $user_attrs = ['update_flag' => true, 'title' => 'Описание файла'];

        $save->execute($aRef, $testFileSetting, $user_attrs);

        $owner_name = $aRef->getType()->getName();
        $owner_id   = $aRef->getId();
        $file_name = $testFileSetting->getName();

        $file_eq = $this->dbAgent->fileAttrEq($owner_name, $owner_id, $file_name, 'title', 'Описание файла');

        $this->assertTrue($file_eq, 'save');
    }

    /**
     * @depends testSaveOperation
     */
    public function cleanOperation()
    {
        $clean = new CleanOperation($this->pathResolver, $this->taxonomy, $this->dbAgent);

        $blockType = $this->taxonomy->getType('test_block');
        $testBlockFields = $blockType->getOwns()->getTyped('file');
        $testFileField = $testBlockFields->getField('test_file1');
        $testFileSetting = $this->fileSettngs->getFile($testFileField);
        $aRef = new ARef($blockType, 0);

        $clean->execute($aRef, $testFileSetting);

        $file_dir = $this->pathResolver->getFileDir();

        $path_file = $file_dir.'/test_block_0_test_file1.txt';

        //Проверка очищенности файлов
        $this->assertFileNotExists($path_file);
    }

    /**
     * @depends testSaveOperation
     */
    public function deleteOperation()
    {
        $delete = new DeleteOperation($this->pathResolver, $this->taxonomy, $this->dbAgent);

        $blockType = $this->taxonomy->getType('test_block');
        $testBlockFields = $blockType->getOwns()->getTyped('file');
        $testFileField = $testBlockFields->getField('test_file1');
        $testFileSetting = $this->fileSettngs->getFile($testFileField);
        $aRef = new ARef($blockType, 0);

        $delete->execute($aRef, $testFileSetting);

        $owner_name = $aRef->getType()->getName();
        $owner_id   = $aRef->getId();
        $file_name = $testFileSetting->getName();

        $file_exist = $this->dbAgent->fileExist($owner_name, $owner_id, $file_name);

        $this->assertFalse($file_exist);
    }

}
