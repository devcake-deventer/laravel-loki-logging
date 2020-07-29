<?php

namespace Devcake\LaravelLokiLogging;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class L3Persister extends Command
{
    protected $signature = 'loki:persist';
    protected $description = 'Persist recent log messages to loki';

    public function handle()
    {
        $file = $file = storage_path(L3ServiceProvider::LOG_LOCATION);
        if (!file_exists($file)) return;

        $content = file_get_contents($file);
        file_put_contents($file, '');

        $messages = explode("\n", $content);
        if (count($messages) === 0) return;

        $http = Http::withBasicAuth(
            config('l3.loki.username'),
            config('l3.loki.password')
        );
        $path = config('l3.loki.server') . "/loki/v1/push";
        foreach ($messages as $message) {
            if ($message === "") continue;
            $data = json_decode($message);
            $resp = $http->post($path, [
                'streams' => [[
                    'stream' => $data->tags,
                    'values' => [[
                        strval($data->time * 1000),
                        $data->message
                    ]]
                ]]
            ]);
        }
    }
}
