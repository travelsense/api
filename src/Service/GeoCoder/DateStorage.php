<?php
namespace Api\Service\GeoCoder;

use Api\Exception\DateFormatException;
use DateTime;

class DateStorage
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
     * @return DateTime|null
     */
    public function readLastUpdatedTime()
    {
        if (file_exists($this->file_name)) {
            $response = file_get_contents($this->file_name);
            $date_time = DateTime::createFromFormat('Y-m-d H:i:s', $response);
            if (!$date_time) {
                throw new DateFormatException();
            }
            return $date_time;
        } else {
            return null;
        }
    }

    /**
     * @param DateTime $date_time
     */
    public function writeLastUpdatedTime(DateTime $date_time)
    {
        $date = $date_time->format('Y-m-d H:i:s');
        file_put_contents($this->file_name, $date);
    }
}
