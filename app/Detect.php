<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detect extends Model
{
    // FIRST create a function to call the comprehend facade and parse the results (below will return an array with the overall sentiment as well as positive/negative scores)

    public function sentimentAnalysis($comments) {

        $results = array();

        if(count($comments)>0) {
            $config = [
                'LanguageCode' => 'en',
                'TextList' => $comments,
            ];

            $jobSentiment = \Comprehend::batchDetectSentiment($config);

            $positive = array();
            $negative = array();

            if(count($jobSentiment['ResultList'])) {
                foreach($jobSentiment['ResultList'] as $result){
                    $positive[] = $result['SentimentScore']['Positive'];
                    $negative[] = $result['SentimentScore']['Negative'];
                }
            }

            $results['positive'] = array_sum($positive)/count($positive);
            $results['negative'] = array_sum($negative)/count($negative);
            $results['sentiment'] = ($results['positive'] > $results['negative'] ? 'POSITIVE' : 'NEGATIVE');

            return $results;
        } else {
            return $results['sentiment'] = 'INVALID';
        }
    }

}
