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
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => WEBHOOKURL,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json;charset=UTF-8'
            ),
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => file_get_contents('php://input'),
            CURLOPT_RETURNTRANSFER => true
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
}
