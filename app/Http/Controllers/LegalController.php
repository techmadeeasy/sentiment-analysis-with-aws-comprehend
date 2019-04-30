<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Legal;
use Illuminate\Support\Facades\Storage;

class LegalController extends Controller
{
        public function analyze(){
            $allfiletext = Storage::disk("s3")->allFiles("text");
            $x = 0;
            foreach($allfiletext as $file){
                $filename = explode("/",$file);
                $casenumber = str_replace(".txt", "", $filename[1]);
                $checkifmedia = str_replace("ms", "", $casenumber, $count);
                if($count>0){
                   continue;
                }
                echo $casenumber . "<br>";
                $openfile = Storage::disk("s3")->get($file);
                $analyze = \App::make("SentimentAnalysis")->decision($openfile);
                $act = preg_match("/Act\s\d+\sof\s\d+/", $openfile, $matches);
                $trim = str_replace("\n", "", $openfile);
                $summary =  preg_match("/Summary:.+ORDER/", $trim, $match);
                $summaryclean = preg_match("/Summary:.+[.]/", $match[0], $res);
                if(count($match)==0){
                    $match[] = "Nothing";
                }
                if(count($matches)==0){
                    $matches[] = "Nothing";
                }
              $legal = Legal::create(["casenumber"=>$casenumber, "acts"=>$matches[0], "summary"=>$match[0],
                  "complete_case"=>$openfile, "sentiment"=>$analyze]);
            }
    }
                public function  ViewFiles(){
                        $legal = new Legal;
                        $sum = Legal::findorFail(923);
                       // $see = $sum->get();
                        return $sum->complete_case;
                }
}
