<?php

namespace MyApp\Logger;

interface LoggerInterface
{
    const TYPE_SCREEN = 1;
    const TYPE_FILE = 2;
    const TYPE_DB = 3;
    const TYPE_LOGGER_BUFFER = 4;
    const TYPE_FILE_BUFFER = 5;
    const TYPE_DEBUG_LOGGER = 6;

    /**
     * Залогировать событие
     * @param string $message - сообение
     * @param string $file - файл, где быловызвано событие
     * @param int $line - строка файла
     * @param string $function - функция/метод
     */
    public function logEvent(string $message, string $file, int $line, string $function);

    /**
     * Вернет тип логгера
     * @return int - тип
     */
    public function getType();

}
