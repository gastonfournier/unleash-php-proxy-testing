<?php
require_once 'vendor/autoload.php';
use Unleash\Client\UnleashBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

$appName = 'unleashPhpProxySDK';
$instanceId = $appName;
sleep(1); // Wait 1 second until edge is ready
$appUrl = 'http://edge:3063/api';

// Read apiKey from /key.txt file
#$apiKey = '*:development.8a3bb93655d6d83d221db475a944b32cdf786c4fea124e10d231caf5';
$apiKey = trim(file_get_contents('/token/frontend.txt'));

$psr6Cache = new FilesystemAdapter('unleash-examples-cache', 0, __DIR__ . '/cache');
$cache = new Psr16Cache($psr6Cache);

$unleash = UnleashBuilder::create()
    ->withAppName($appName)
    ->withAppUrl($appUrl)
    ->withInstanceId($instanceId)
    ->withCacheHandler($cache)
    ->withMetricsEnabled(false)
    ->withCacheTimeToLive(3)
    ->withMetricsInterval(1)
    ->withHeader('Authorization', $apiKey)
    ->withProxy($apiKey)
    ->build();

$evaluations = 250;
$enabledCount = 0; // Initialize counter for enabled toggles
$toggle = 'a_toggle_100';

$unleash->isEnabled($toggle); // warm up

// measure time taken to evaluate 25 times
$startTime = microtime(true);
for ($i = 1; $i <= $evaluations; $i++) {
    //$unleash->set_context
    if ($unleash->isEnabled($toggle)) {
        echo "{$i}: {$toggle} is enabled \n";
        $enabledCount++;
    } else {
        echo "{$i}: {$toggle} is disabled \n";
    }
}
$endTime = microtime(true);

sleep(1); // Wait 1 second before printing metrics
$timeInMs = ($endTime - $startTime) * 1000;
$avg = $timeInMs/$evaluations;
echo "[{$timeInMs}ms] (average of {$avg}ms per eval) {$toggle} was enabled {$enabledCount} out of {$evaluations} times.\n";
