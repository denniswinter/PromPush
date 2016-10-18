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

namespace Dew\PromPush;

use GuzzleHttp\Client as HttpClient;

class Client
{

    /** @var HttpClient */
    private $httpClient;

    /**
     * Client constructor.
     *
     * @param HttpClient $httpClient
     *
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Replaces all metrics of given job
     *
     * @param array  $data
     * @param string $job
     * @param array $group
     */
    public function set(array $data, $job, array $group = null)
    {
        $this->doRequest($data, $job, $group, 'put');
    }

    /**
     * Replaces only previously pushed metrics of the same name and job
     *
     * @param array  $data
     * @param string $job
     * @param array $group
     */
    public function replace(array $data, $job, array $group = null)
    {
        $this->doRequest($data, $job, $group, 'post');
    }

    /**
     * Deletes all entries for given job and group
     *
     * @param string $job
     * @param array $group
     */
    public function delete($job, array $group = null)
    {
        $this->doRequest(null, $job, $group, 'delete');
    }

    /**
     * @param array  $data
     * @param string $job
     * @param array  $group
     * @param string $method
     */
    protected function doRequest(array $data, $job, $group, $method)
    {
        $url = "/metrics/job/{$job}";

        if (is_array($group)) {
            foreach ($group as $label => $value) {
                $url .= "/{$label}/{$value}";
            }
        }

        $reqOptions = [];
        if ($method != 'delete') {
            $dataString = implode("\n", $data);
            $reqOptions['body'] = $dataString;
        }

        $this->getHttpClient()->request(strtoupper($method), $url, $reqOptions);
    }
}