# PromPush
[![Build Status](https://travis-ci.org/denniswinter/PromPush.svg?branch=master)](https://travis-ci.org/denniswinter/PromPush)

Simple HttpClient wrapper of Prometheus PushGateway.

## Installation

Using Composer:

```bash
composer require denniswinter/prompush
```

## API

Instantiate client using:

```php
<?php
$client = new PromPush\Client(new GuzzleHttp\Client([
    'base_uri' => 'http://prometheus-pushgateway.example.com:9091']
));
```

Push metrics to Gateway:

```php
<?php
$data = [
    '# TYPE test_test_test summary',
    'test_test_test_sum{handler="push"} ' . $sum . "\n",
    'test_test_test_count{handler="push"} ' . $i . "\n"
];

$job = 'test_job';

$group = array('test_group_1', 'test_group_2');

$client->set($data, $job, $group);
```

Replace metrics on Gateway:

```php
<?php
$data = [
    '# TYPE test_test_test summary',
    'test_test_test_sum{handler="push"} ' . $sum . "\n",
    'test_test_test_count{handler="push"} ' . $i . "\n"
];

$job = 'test_job';

$group = array('test_group_1', 'test_group_2');

$client->replace($data, $job, $group);
```

Delete metrics on Gateway:

```php
<?php

$job = 'test_job';

$group = array('test_group_1', 'test_group_2');

$client->replace($job, $group);
```