<?php
namespace Api\Service\GeoCoder;

use DateTime;

class DateWriteReader
{
    private $file_name;

    public function __construct(string $file_name)
    {
        $this->file_name = $file_name;
    }

    public function readLastUpdatedTime()
    {
        return file_get_contents($this->file_name);
    }

    public function writeLastUpdatedTime(DateTime $date_time)
    {
        $file = file($this->file_name);
        file_put_contents($file, $date_time);
    }
}
