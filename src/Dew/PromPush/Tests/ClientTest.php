<?php
/**
 * Dwinter Prometheus Metrics
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to winter@babymarkt.de so I can send you a copy immediately.
 */

namespace Dew\PromPush\Tests;

use Dew\PromPush\Client;
use GuzzleHttp\Client as HttpClient;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function dataProvider()
    {
        $out = [];

        $method  = 'put';
        $url     = 'localhost:9090';
        $job     = 'testJob';
        $group   = array('testGroup');
        $data    = array('test.metric_1 1', 'test.metric_2 1');
        $out['should trigger put request'] = [$method, $url, $job, $group, $data];
        $method  = 'delete';
        $url     = 'localhost:9090';
        $job     = 'testJob';
        $group   = array('testGroup');
        $out['should trigger delete request'] = [];
        $method  = 'post';
        $url     = 'localhost:9090';
        $job     = 'testJob';
        $group   = array('testGroup');
        $data    = array('test.metric_1 1', 'test.metric_2 1');
        $out['should trigger post request'] = [];

        return $out;
    }

    /**
     * @dataProvider dataProvider
     */
    public function testClient($url, $job, $group, $method, $data = null)
    {
        $mock = $this->getHttpClientMock($url, $job, $group, $method, $data);
        $client = new Client($mock);

        var_dump($client);

        switch ($method) {
            case 'delete':
                $client->delete($job, $group);
                break;
            case 'put':
                $client->set($data, $job, $group);
                break;
            case 'post':
                $client->replace($data, $job, $group);
                break;
        }
    }

    protected function getHttpClientMock($method, $url, $job, $group, $data = null)
    {
        $expectedUrl = "/metrics/job/{$job}";

        if (is_array($group)) {
            foreach ($group as $label => $value) {
                $expectedUrl .= "/{$label}/{$value}";
            }
        }
        if ($data !== null) {
            $body = implode("\n", $data);
            $mock = $this->getMockBuilder(HttpClient::class)
                ->setConstructorArgs(array($url))
                ->getMock();
            $mock->expects($this->any())
                ->method('request')
                ->with($this->equalTo($method), $this->equalTo($expectedUrl), $this->equalTo(array('body'=>$body)));
        } else {
            $mock = $this->getMockBuilder(HttpClient::class)
                ->setConstructorArgs(array($url))
                ->getMock();
            $mock->expects($this->any())
                ->method('request')
                ->with($this->equalTo($method), $this->equalTo($expectedUrl));
        }
    }
}