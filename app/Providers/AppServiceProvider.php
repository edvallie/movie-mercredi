<?php

namespace App\Providers;

use App\Services\DynamoDbService;
use Aws\DynamoDb\DynamoDbClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DynamoDbClient::class, function () {
            $config = [
                'region' => config('dynamodb.region'),
                'version' => 'latest',
            ];

            if ($endpoint = config('dynamodb.endpoint')) {
                $config['endpoint'] = $endpoint;
                $config['credentials'] = [
                    'key' => env('AWS_ACCESS_KEY_ID', 'local'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY', 'local'),
                ];
            }

            return new DynamoDbClient($config);
        });
    }

    public function boot(): void
    {
        if (config('dynamodb.endpoint')) {
            app(DynamoDbService::class)->ensureTableExists();
        }
    }
}
