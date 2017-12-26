<?php

namespace NotArchid\SmsGateway;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * 
     * @var boolean
     */
    protected $defer = false;

    /**
     * An instance of GuzzleHttp's client.
     * 
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * SmsGateway.Me account's email address.
     * 
     * @var string
     */
    protected $email;

    /**
     * SmsGateway.Me account's password.
     * 
     * @var string
     */
    protected $password;

    /**
     * The ID of the device you wish to send the message
     * from.
     * 
     * @var int
     */
    protected $device_id;

    public function boot()
    {
        $this->client = new Client();
        $this->email = config('services.smsgateway.email');
        $this->password = config('services.smsgateway.password');
        $this->device_id = config('services.smsgateway.device_id');
    }

    /**
     * Register the service provider.
     *
     * @throws \Exception
     * @return void
     */
    public function register()
    {
        $this->app->singleton('smsgateway', function () {
            return new SmsGateway(
                $this->client,
                $this->email,
                $this->password,
                $this->device_id
            );
        });
    }
}