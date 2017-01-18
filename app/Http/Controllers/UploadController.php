<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use View;
use File;
use Session;
use Redirect;
use Storage;

class UploadController extends Controller {

    public function store(Request $r)
    {
        $file = Input::file('file');
        $inputs = Input::all();
        $rules = array('file' => array('required'));
        $validator = Validator::make([$inputs], $rules);
        if ($validator->fails()) {
            Session::flash('error', 'Jāizvēlas fails!');
            return Redirect::route('upload');
            //return $this->errors(['message' => 'Only .zip and .json files allowed!', 'code' => 400]);
        }
        $destinationPath = storage_path() . '/uploads/';

        $filename = uniqid()."_".$file->getClientOriginalName();
        if(!$file->move($destinationPath, $filename)) {
            //return $this->errors(['message' => 'Error saving the file.', 'code' => 400]);
            Session::flash('error', 'Error saving the file!');
            return Redirect::back();
        }

        $location = $destinationPath.$filename;


        if(File::extension($location) != 'zip' && File::extension($location) != 'json')
        {
            Session::flash('error', 'Only .zip and .json files allowed!');
            File::delete(storage_path().'/uploads/'.$filename);
            return Redirect::back();
        }

        if(File::extension($location) == 'zip')
        {
            $zip = new \ZipArchive();
            
            if ($zip->open($location) === TRUE)
            {
                for($i=0; $i<$zip->numFiles; $i++)
                {
                    $zip->renameName($zip->getNameIndex($i), uniqid()."_".$zip->getNameIndex($i));
                    //echo $zip->getNameIndex($i)."<br />";
                }
                $zip->close();
                $zip->open($location);
                $zip->extractTo($destinationPath);
                $zip->close();
                
                
            }
            //if(!File::delete('storage/uploads/'.$filename)) exit('File delete error!'); 
            File::delete(storage_path().'/uploads/'.$filename);
            Session::flash('success', '.zip uploaded and extracted!');
            return Redirect::back();

        }
        //return response()->json(['success' => true], 200);
        Session::flash('success', 'File uploaded!');
        return Redirect::back();
    }

    public function import()
    {
        $data = File::files(storage_path() . '/uploads/');
        return View::make('data.list', compact('data'));
    }

    public function upload()
    {
        $data = "";
        return View::make('data.upload', compact('data'));
    }
}