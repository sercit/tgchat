<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Web\WebDriver;
use BotMan\BotMan\Cache\LaravelCache; // The secret sauce
class Bot
{
    protected $botman;

    public function __construct()
    {
        $config = [
            'conversation_cache_time' => 40, // Cache settings
            'user_cache_time' => 30, // Cache settings
            'telegram' => [ // Bringing in the web driver config
                'token' => '847119911:AAGA-qJu9WfPqQYFb7e0WTwt8QAfA0av7mo'
            ]
        ];

        DriverManager::loadDriver(WebDriver::class);

        $this->botman = BotManFactory::create($config, new LaravelCache(), app()->make('request')); // Bring in the request!
    }

    public function getBotMan()
    {
        return $this->botman; // Boom. 💥
    }
}