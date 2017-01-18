<?php

require_once './vendor/autoload.php';

use Lpdigital\Github\Parser\WebhookResolver;

const WEBHOOKURL = "https://discordapp.com/api/webhooks/XXXXXXXXX/YYYYYYYYY";

// GitHub send data using POST method
if ('POST' === $_SERVER['REQUEST_METHOD']) {
    $decodedJson = json_decode(file_get_contents('php://input'), true);
    $resolver    = new WebhookResolver();
    $event       = $resolver->resolve($decodedJson); // for ex, we get instance of PullRequestEvent
    if (($event instanceof Lpdigital\Github\EventType\PushEvent) || ($event instanceof Lpdigital\Github\EventType\PullRequestEvent)) {
        if ($event instanceof Lpdigital\Github\EventType\PushEvent) {
            $msg = "Un nouveau commit a été fait sur ".$event->getRepository()->getFullName()." : ";
            foreach ($event->commits as $commit) {
                $msg .= $commit["url"]." ";
            }
        } else if ($event instanceof Lpdigital\Github\EventType\PullRequestEvent) {
            $msg = "Un nouveau pull request a été '".$event->getAction()."' sur ".$event->getRepository()->getFullName()." : ".$event->pullRequest->getHtmlUrl();
        }
        $data = json_encode(array(
            'content' => $msg,
        ));
        do_post_request(WEBHOOKURL, $data, "content-type: application/json");
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
