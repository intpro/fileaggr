<?php

namespace Interpro\FileAggr\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Interpro\FileAggr\Contracts\CommandAgents\OperationsAgent;
use Interpro\FileAggr\Contracts\Settings\PathResolver;

class FileOperationController extends Controller
{
    private $operationsAgent;
    private $pathResolver;

    public function __construct(OperationsAgent $operationsAgent, PathResolver $pathResolver)
    {
        $this->operationsAgent = $operationsAgent;
        $this->pathResolver = $pathResolver;
    }

    public function testpage()
    {
        return view('filetest');
    }

    private function same(Request $request, $operation)
    {
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'entity_name' => 'required',
                    'file_name' => 'required',
                    'entity_id' => 'integer|min:0'
                ]
            );

            if($validator->fails()){
                return ['error'=>true, 'error'=>$validator->errors()->setFormat(':message<br>')->all()];
            }

            $entity_name = $request->input('entity_name');
            $file_name = $request->input('file_name');

            if($request->has('entity_id'))
            {
                $entity_id = (int) $request->input('entity_id');
            }
            else
            {
                $entity_id = 0;
            }

            $this->operationsAgent->$operation($entity_name, $entity_id, $file_name);

            return ['error'=>false]; //какой-то item мимо экстрактора
        }
        catch(\Exception $e)
        {
            return ['error'=>true, $e->getMessage()];
        }
    }

    public function clean(Request $request)
    {
        return $this->same($request, 'clean');
    }

    public function upload(Request $request)
    {
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'entity_name' => 'required',
                    'file_name' => 'required',
                    'entity_id' => 'integer|min:0',
                    'file' => 'required|file|max:5120',
                ]
            );

            if($validator->fails()){
                return ['error'=>true, 'error'=>$validator->errors()->setFormat(':message<br>')->all()];
            }

            $entity_name = $request->input('entity_name');
            $file_name = $request->input('file_name');
            $file = $request->file('file');

            if($request->has('entity_id'))
            {
                $entity_id = (int) $request->input('entity_id');
            }
            else
            {
                $entity_id = 0;
            }

            $ext = $file->guessClientExtension();
            $file_path = $this->pathResolver->getTmpPath().'/'.$entity_name.'_'.$entity_id.'_'.$file_name.'.'.$ext;

            $this->operationsAgent->upload($entity_name, $entity_id, $file_name, $file);

            return ['error'=>false, 'link' => $file_path];
        }
        catch(\Exception $e)
        {
            return ['error'=>true, $e->getMessage()];
        }
    }
}
