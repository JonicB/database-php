<?php

namespace MyApp\Logger;

class FileLoggerBuf extends BaseLogger{

    protected  $fileName;
    protected $fileHandler;

    public function __construct(string $fileName){
        if( !file_exists( $fileName ) ) {
            throw new Exception('File '.$fileName.' does not exist');
        }
        $this->fileName = $fileName;
        $this->openFileForLog();
        parent::__construct(LoggerInterface::TYPE_FILE_BUFFER);
    }

    protected function setFileHandler( $fileHandler ){
        if( !is_resource( $fileHandler ) ) {
            throw new TypeError('invalid argument, must be a resource for fileHandler');
        }
        $this->fileHandler = $fileHandler;
    }

    protected function openFileForLog(){
        $fileHandler = fopen( $this->fileName, 'a' );
        $this->setFileHandler($fileHandler);
    }

    public function logEvent(string $message, string $file, int $line, string $function){
        $logRecord = $this->prepareLogRecord($message, $file, $line, $function);
        $logRecordTxt = $this->getLogTextVerbose($logRecord);
        fwrite($this->fileHandler, $logRecordTxt);
    }

    function __destruct(){
        fclose($this->fileHandler);
    }
}