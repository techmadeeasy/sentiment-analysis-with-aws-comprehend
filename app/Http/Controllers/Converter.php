<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Converter extends Controller
{
    public function convert(){

        $file = Storage::disk("s3")->allFiles("2018");
        $client = LambdaClient::factory([
            'version' => 'latest',
            // The region where you have created your Lambda
            'region'  => 'us-east-1',
        ]);
        $count = 1;
        foreach ($file as $f){
            $getname = explode(".", $f); $name = explode("/", $getname[0]); $textfilename =  $name[1]. ".txt";
            if(Storage::disk("s3")->exists("text/$textfilename")){
                continue;
            }
            $text_uri = '"' . "s3://pdfcases/text/$textfilename" . '"';
            $hey = '"' . "s3://pdfcases/$f" . '"';
            $payload = '{"document_uri": '. $hey .',"text_uri": ' .  $text_uri . '}';
            $result = $client->invoke([
                // The name your created Lamda function
                'FunctionName' => 'arn:aws:lambda:us-east-1:457566014547:function:textractor_simple',
                //  'FunctionARN' =>'arn:aws:lambda:us-east-1:457566014547:function:textractor_simple',
                'Payload' =>$payload,
            ]);
            echo $count++ . " converted " . $textfilename;

        }

        echo  $result->get('StatusCode') . "<br>";
        //dd($result);

    }
}
