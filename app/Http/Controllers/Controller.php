<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function processIOSFile($filename)
    {
        $data = file_get_contents("storage/app/translation_files/$filename");
        preg_match_all('/".*"\s=\s".*";/', $data, $test);
        $result = [];
        foreach ($test[0] as $q) {
            $q = str_replace(';', '', $q);
            $q = str_replace('"', '', $q);
            $r = explode(' = ',$q);
            $result[$r[0]] = $r[1];
        }
        //$result is an array of key=>value;
        return $result;
    }

    public function processAndroidFile($filename){
        $dom=new \DOMDocument();
        $dom->load("storage/app/translation_files/$filename");
        $root=$dom->documentElement;
        $strings=$root->getElementsByTagName('string');
        $result=[];
        foreach ($strings as $string) {
            $key = $string->getAttribute('name');
            $value = $string->textContent;
            $result[$key]=$value;
        }
        return $result;
    }

    public function editAndroidFile($filename, $values)
    {
        unset($values["_token"]);
        $dom = new \DOMDocument();
        $dom->load("storage/app/translation_files/$filename");
        $root = $dom->documentElement;
        $strings = $root->getElementsByTagName('string');
        foreach ($strings as $string) {
            $string->textContent = $values[$string->getAttribute('name')];
            unset($values[$string->getAttribute('name')]);
        }
        if($values){
            foreach ($values as $key=>$value) {
                // create tag
                $tag = $dom->createElement('string', $value);
                //create  and append the name attribute
                $attr = $dom->createAttribute('name');
                $attr->value = $key;
                //append the attribute
                $tag->appendChild($attr);
                $root->appendChild($tag);
            }
        }
        $dom->SaveXML();
        $dom->save("storage/app/translation_files/$filename");
    }
    public function editIOSFile($fileName, $values){
        $string='';
        foreach ($values as $key=>$value){
            $string = $string . "\"$key\" = \"$value\"; \r\n";
        }
        file_put_contents("storage/app/translation_files/$fileName",$string);
    }

    public function uploadFile(Request $request){
        $file = request()->file('uploadedFile');
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extension;
        $file->move("storage/app/translation_files", $filename);
        session(['translation_file_name'=>$filename]);
        session(['translation_file_extension'=>$extension]);
        if($extension == 'strings'){
            $values = $this->processIOSFile($filename);
        }
        else{
            $values = $this->processAndroidFile($filename);
        }
        //return view('update_file',['values'=>$values]);
        return $values;
    }

    public function updateFile(Request $request){
        $keys=$request->keys;
        $values=$request->translations;
        $translations=array_combine($keys, $values);
        $filename = session('translation_file_name');
        if((session('translation_file_extension')==='strings')){
            $this->editIOSFile($filename, $translations);
        }
        else{
            $this->editAndroidFile($filename, $translations);
        }
        return view('download',["filename"=>"storage/app/translation_files/$filename"]);
    }
}
