<?php
/**
 * Created by PhpStorm.
 * User: suncco
 * Date: 2018/6/14
 * Time: 11:55
 */

namespace app\admin\controller;

use Ethereum\Ethereum;
use JPush\Client;
class test
{
    public function comp(){
        try {
            $eth = new Ethereum('http://localhost:8445');

//            var_dump($eth);die;
            echo $eth->client->eth_protocolVersion();
        }
        catch (\Exception $exception) {
            die ("Unable to connect.");

        }
    }
}