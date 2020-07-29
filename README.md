# Laravel Loki Logging
_Logging to Loki for Laravel_

## Usage
1. Install this package: `composer require devcake-deventer/laravel-loki-logging`
2. Publish the configuration: `php artisan vendor:publish --provider=Devcake\\LaravelLokiLogging\\L3ServiceProvider`
3. Create a new log channel in `config/logging.php`:
   ```php
   'loki' => [
     'driver' => 'monolog',
     'handler' => L3Logger::class,
   ]
   ```
4. Configure at least the `LOG_CHANNEL`, `LOG_USERNAME` and `LOG_PASSWORD`
    1. Ensure `APP_NAME` is configured appropriately. If this value cannot be changed, use `LOG_APP`.
    2. Optionally configure `LOG_SERVER` and `LOG_FORMAT`
5. Configure the `loki:persist` job to run periodically in your schedule. We recommend every minute, but feel free to
 reduce this.
6. `Log::info('Hello Loki!');`

## Configuration
The behaviour of the logger can be adjusted with the config options below.

|Key|Description|Default|
|---|---|---|
|`context`|Extra variables to be added as labels to the message. Variable substitutions are available.|`application`: `env('LOG_APPLICATION')`<br/>`type`: `'{level_name}'`|
|`format`|How log messages should be formatted. Variable substitutions are available.|`[{level_name}] {message}`|
|`loki.server`|The loki server to which data should be logged.|`https://logging.devcake.app/loki`|
|`loki.username`|Username for HTTP basic authentication, can be left empty.|Env-variable `LOG_USERNAME`|
|`loki.password`|Password for HTTP basic authentication, can be left empty.|Env-variable `LOG_PASSWORD`|
