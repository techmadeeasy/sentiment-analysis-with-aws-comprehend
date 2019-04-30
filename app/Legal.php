<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Legal extends Model
{
    protected $fillable=[
        "casenumber", "acts", "summary", "complete_case", "sentiment"
    ];

    public function getSummaryAttribute($value){
        $header = preg_replace("/Summary:/", "<h1>Summary:</h1>", $value);
       // $res = strtoupper($match[0]);
        $rm = str_replace("ORDER", "", $header);
        if($dash = preg_replace("/\d[_]+/", "", $rm)){
            return $dash;
        }
        return $rm;
    }

    public function getCompleteCaseAttribute($value){
        $header = preg_replace("/^.+AFRICA/", "<h1>THE SUPREME COURT OF APPEAL OF SOUTH AFRICA</h1>", $value);
        $matter = preg_replace("/In.+between:/", "<strong>In the matter between:</strong><br>", $header);

        return $matter;
    }
}
