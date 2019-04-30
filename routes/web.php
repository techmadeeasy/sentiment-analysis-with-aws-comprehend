<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
ini_set("max_execution_time", 600);

use Antoineaugusti\LaravelSentimentAnalysis\Facades\SentimentAnalysis as SentimentAnalysis;
use App\Detect;
use Aws\Comprehend\ComprehendClient;
use Aws\Lambda\LambdaClient;

Route::get('/', function () {
    return view('welcome');
});
Route::get("/are", function (){

$detect = new Detect;
    $comments = [
        'I think this is very good considering I created a package/wrapper for Amazon Comprehend. Yay me!',
        'Oh my good this is such a bloody rubbish package/wrapper. I hope the author stops coding immediately.',
        'This is really good, I really love this stand by this',
        'This is sooooo bad'
    ];

    dd($detect->sentimentAnalysis($comments));
});

Route::get("/test", function (){
    $client = new ComprehendClient([ 'region' => 'us-east-2','version' => 'latest']);
    $result = $client->startEntitiesDetectionJob([
        'ClientRequestToken' => '',
        'DataAccessRoleArn' => "arn:aws:iam::457566014547:user/comprehend", // REQUIRED
        'InputDataConfig' => [ // REQUIRED
            'InputFormat' =>"ONE_DOC_PER_LINE",
            'S3Uri' =>  "s3://pdfcases/2016/", // REQUIRED
        ],
        'JobName' => 'larajobs',
        'LanguageCode' => 'en', // REQUIRED
        'OutputDataConfig' => [ // REQUIRED
            'KmsKeyId' => '',
            'S3Uri' => "s3://pdfcases/2016/", // REQUIRED
        ],
        'VolumeKmsKeyId' => '',
    ]);

//    $result = $client->describeKeyPhrasesDetectionJob([
//        'JobId' => '<string>', // REQUIRED
//    ]);


exit;


    $file = Storage::disk('s3')->get("text/sca2018-191.txt");
   // return strlen($file) ;
    $file = explode("'n\'", $file);
    $config = [
        'LanguageCode' => 'en',
        'TextList' => $file,
    ];

    $jobSentiment = \Comprehend::batchDetectKeyPhrases($config);
    $store = Storage::disk("s3")->put($jobSentiment['ResultList'], "sca2018-191.pdf");

    dd($jobSentiment['ResultList']);
});

Route::get("/test2", function () {
    $file_content = Storage::disk('s3')->get("text/anew61.txt");
    $trim = str_replace("\n", " ", $file_content);
    $find = preg_match("/Summary:.+ORDER/", $trim, $matches);
    // $file = 'Oh my gosh this is such a bloody rubbish package/wrapper. I hope the author stops coding immediately.';
    $analyze = App::make("SentimentAnalysis")->scores($file_content);

   // return $matches;
   return $trim;
});


Route::get("/convert", "Converter@convert");
Route::get("/list", "LegalController@analyze");
Route::get("/view", "LegalController@ViewFiles");
