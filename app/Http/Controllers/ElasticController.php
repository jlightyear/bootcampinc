<?php

namespace App\Http\Controllers;

use Elasticsearch\Client as Es;
# use Elasticsearch\Client;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Redis;

class ElasticController extends Controller
{
    /**
     * Publishes a 'stop' event to Redis Pub/Sub 'daemon-notify' channel
     *
     * @return Response
     */
    public function testEs()
    {

        $searchParams['index'] = 'mongoindex';
        $searchParams['size'] = 0;

        $search = '{
                    "aggs": {
                        "tweets": {
                            "filter": {
                                "term": {
                                    "tweet.text": "barcelona"
                                }
                            },
                            "aggs": {
                                "histogram_tweets": {
                                    "histogram": {
                                        "field": "tweet.created_at",
                                        "interval": 1500000
                                    }
                                }
                            }
                        }
                    },
                    "size": 0
                }';
        $searchParams['body'] = json_decode($search);

        $elastic = new Es();

        $result = $elastic->search($searchParams);

        //$result = Es::search($searchParams);
        return response()
            ->json($result['aggregations']['tweets']['histogram_tweets']['buckets'])
            ->header('Content-Type', 'application/json');



    }
    public function testEs2(){

        $searchParams['index'] = 'mongoindex';
        $searchParams['size'] = 50;
        $searchParams['body'] = json_decode('
            {
              "query": {
                        "bool": {
                            "must": [
                    {
                        "range": {
                        "tweet.created_at": {
                            "from": "now-1d",
                          "to": "now"
                        }
                      }
                    }
                  ],
                  "must_not": [],
                  "should": []
                }
              },
              "from": 0,
              "size": 0,
              "sort": [],
              "facets": {
                        "histo1": {
                            "histogram": {
                                "field": "created_at",
                    "time_interval": "1.5h"
                  }
                }
              }
            }'
        );

        $elastic = new Es();

        $result = $elastic->search($searchParams);

        //$result = Es::search($searchParams);
        return response()
            ->json($result)
            ->header('Content-Type', 'application/json');
    }

    public function testEs3(){

        $searchParams['index'] = 'mongoindex';
        $searchParams['size'] = 50;
        $searchParams['body']['query']['query_string']['query'] = 'foofield:barstring';

        $elastic = new Es();

        $result = $elastic->search($searchParams);

        //$result = Es::search($searchParams);
        return response()
            ->json($result)
            ->header('Content-Type', 'application/json');
    }

}
