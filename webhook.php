<?php

require_once './vendor/autoload.php';

use Lpdigital\Github\Parser\WebhookResolver;

const WEBHOOKURL = "https://discordapp.com/api/webhooks/270664905894526986/QxJiz05kbdZ3A6xvGkVYfa5f7lz1bE4QlKv81dRC7KrYFv77_nAonRzch91pS29XtTTg/github";

// GitHub send data using POST method
if ('POST' === $_SERVER['REQUEST_METHOD']) {
    // get the POST Request Body
    $decodedJson = json_decode(file_get_contents('php://input'), true);
    $resolver    = new WebhookResolver();
    $event       = $resolver->resolve($decodedJson); // for ex, we get instance of PullRequestEvent
    //file_put_contents('events.log', PHP_EOL. get_class($event)." - ".$event->getRepository()->getFullName()." : ".$event::name(), FILE_APPEND | LOCK_EX);
    if (($event instanceof Lpdigital\Github\EventType\PushEvent) || ($event instanceof Lpdigital\Github\EventType\PullRequestEvent)) {
        file_put_contents('events.log', PHP_EOL. get_class($event)." - ".$event->getRepository()->getFullName()." : ".$event::name(), FILE_APPEND | LOCK_EX);
        // cURL away!
        do_post_request(WEBHOOKURL, file_get_contents('php://input'), "content-type: application/json");
    }
}

function do_post_request($url, $data, $optional_headers = null)
{
    $params = array('http' => array(
              'method' => 'POST',
              'content' => $data
            ));
    if ($optional_headers !== null) {
        $params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if (!$fp) {
        throw new Exception("Problem with $url, $php_errormsg");
    }
    $response = @stream_get_contents($fp);
    if ($response === false) {
        throw new Exception("Problem reading data from $url, $php_errormsg");
    }
    return $response;
}
