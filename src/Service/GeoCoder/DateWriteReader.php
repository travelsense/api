<?php
namespace Api\Service\GeoCoder;

use DateTime;

class DateWriteReader
{
    /**
     * @var string
     */
    private $file_name;

    public function __construct(string $file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * @return null|string
     */
    public function readLastUpdatedTime()
    {
        if (file_exists($this->file_name)) {
            return file_get_contents($this->file_name);
        } else {
            return null;
        }
    }

    /**
     * @param string $date_time
     */
    public function writeLastUpdatedTime(string $date_time)
    {
        file_put_contents($this->file_name, $date_time);
    }
}
