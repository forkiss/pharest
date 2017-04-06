<?php

namespace Pharest\Register;

class Logger
{

    protected $date;

    protected $time;

    protected $path;

    /**
     * Logger constructor.
     *
     * @param \Pharest\Config $config
     */
    public function __construct(\Pharest\Config $config)
    {
        $this->path = $config->app->get('log');

        $this->date = $config->time[0];

        $this->time = $config->time[1];
    }

    public function error()
    {
        if (!$this->path) {
            exit;
        }

        $_error = error_get_last();

        if (!empty($_error) and in_array($_error['type'], [1, 4, 16, 64, 256, 4096, E_ALL])) {
            $path = APP_ROOT . $this->path . 'error/';

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $file = $path . 'error-log-' . $this->date . '.txt';

            $message = $this->time . ' - ' . json_encode([
                    'type'    => $_error['type'],
                    'message' => substr($_error['message'], 0, strpos($_error['message'], "\nStack trace")),
                    'file'    => str_replace(APP_ROOT, '', $_error['file']),
                    'line'    => $_error['line']
                ], JSON_UNESCAPED_SLASHES) . "\n";

            file_put_contents($file, $message, FILE_APPEND);
        }

        exit;
    }

}