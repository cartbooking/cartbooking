<?php

namespace CartBooking\Lib\Utilities;

class FileSystem
{
    /** @var string  */
    private $cachePath;

    public function __construct(array $settings)
    {
        $this->cachePath = $settings['cache'];
    }

    public function fopen($filename, $mode)
    {
        return fopen($this->cachePath . $filename, $mode);
    }

    public function fputcsv($handle, array $fields, $delimiter = ",", $enclosure = '"', $escape_char = "\\")
    {
        return fputcsv($handle, $fields, $delimiter, $enclosure, $escape_char);
    }

    public function fclose($handle)
    {
        return fclose($handle);
    }
}
