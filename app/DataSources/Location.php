<?php

namespace DataSources;

class Location {
    public static function last_location()
    {
        $line = '';
        $filename = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'location.log';

        $f = fopen($filename, 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        /**
         * Trim trailing newline chars of the file
         */
        while ($char === "\n" || $char === "\r") {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        /**
         * Read until the start of file or first newline char
         */
        while ($char !== false && $char !== "\n" && $char !== "\r") {
            /**
             * Prepend the new char
             */
            $line = $char . $line;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        list($time, $lat, $lon) = explode(',', $line);
        return (object)[
            'lat' => $lat,
            'lon' => $lon,
            'time' => $time
        ];
    }
}