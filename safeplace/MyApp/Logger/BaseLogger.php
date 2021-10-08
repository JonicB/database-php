<?php

namespace MyApp\Logger;

abstract class BaseLogger implements LoggerInterface
{
    protected $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function getType(){
        return $this->type;
    }

    protected final function prepareLogRecord( $message, $file, $line, $function ){
        return [
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'function' => $function,
            'user' => 1,
            'date' => date('d.m.Y H:m:s', time())
        ];
    }

    protected function getLogTextVerbose( array $lr ){
        return "LOG type(".$this->getType().") [".$lr['date']."]: ".$lr['message']." at ".$lr['file']." line ".$lr['line'].PHP_EOL;
    }
}
