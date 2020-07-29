<?php

namespace Devcake\LaravelLokiLogging;


use Monolog\Handler\HandlerInterface;

class L3Logger implements HandlerInterface
{
    /** @var resource */
    private $file;
    /** @var boolean */
    private $hasError;
    /** @var array */
    private $context;
    /** @var string */
    private $format;

    public function __construct(string $format = '[{level_name}] {message}', array $context = [])
    {
        $this->format = config('l3.format');
        $this->context = config('l3.context');

        $file = storage_path(L3ServiceProvider::LOG_LOCATION);
        if (!file_exists($file)) {
            touch($file);
        }
        $this->file = fopen($file, 'a');
        register_shutdown_function([$this, 'flush']);
    }

    /**
     * This handler is capable of handling every record
     * @param array $record
     * @return bool
     */
    public function isHandling(array $record): bool
    {
        return true;
    }

    public function handle(array $record): bool
    {
        $this->hasError |= $record['level_name'] === 'ERROR';
        $message = $this->formatString($this->format, $record);
        $tags = array_merge($record['context'], $this->context);
        foreach ($tags as $tag => $value) {
            if (is_string($value)) {
                $tags[$tag] = $this->formatString($value, $record);
            } else {
                unset($tags[$tag]);
            }
        }
        return fwrite($this->file, json_encode([
                'time' => now()->getPreciseTimestamp(),
                'tags' => $tags,
                'message' => $message
            ]) . "\n");
    }

    public function handleBatch(array $records): void
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    public function flush($force = false): void
    {
        if ($this->hasError || $force) {
            $persister = new L3Persister();
            $persister->handle();
        }
    }

    public function close(): void
    {
        fclose($this->file);
    }

    private function formatString(string $format, array $context): string
    {
        $message = $format;
        foreach ($context as $key => $value) {
            if (!is_string($value)) continue;
            $message = str_replace(
                sprintf('{%s}', $key),
                $value,
                $message
            );
        }
        return $message;
    }
}
