<?php

include __DIR__.'/vendor/autoload.php';

use Lpdigital\Github\Parser\WebhookResolver;

$discord = new \Discord\Discord([
    'token' => 'MjY5NzYzOTA3NzAxMTc4MzY4.C1uiNQ.nbAjQSPK0PADX1Cpa2mVpVxWIDA',
    'loadAllMembers' => true
]);

$discord->on('ready', function ($discord) {
    echo "Bot is ready.", PHP_EOL;
    
    //$guild = $discord->guilds->get('id', 'MilredelLand');
    
    //$channel = $guild->channels->get('id', 'general');
    //print_r($guild->channels);
    /*while(true) {
        $discord->channel
        usleep(100);
    }*/
    //$channel->sendMessage("coucou tout le monde !");

    // Listen for events here
    $discord->on('message', function ($message, $discord) {
        echo "Recieved a message from {$message->author->username}: {$message->content}", PHP_EOL;
        echo ">".count($message->channel->guild->channels)."<";
        $channels = $message->channel->guild->channels;
        $mainChannel = $channels->get('id', 'tagada');
        $mainChannel->sendMessage("ok, roger");
        //$member = $message->channel->guild->members->first();
        //$message->channel->sendMessage("I read your message : {$message->content}");
        //$discord->sendMessage("I read your message : {$message->content}");
    });
});

$discord->run();
