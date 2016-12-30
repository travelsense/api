<?php
namespace Api\Service\GeoCoder;

use DateTime;
use PHPUnit_Framework_TestCase;

class DateStorageTest extends PHPUnit_Framework_TestCase
{
    private $date_write_reader;
    private $file_name = '/tmp/last_update_test.txt';

    public function setUp()
    {
        $this->date_write_reader = new DateStorage($this->file_name);
    }

    public function testReadLastUpdatedTime()
    {
        $last_date = $this->date_write_reader->readLastUpdatedTime();
        $this->assertEquals(null, $last_date);

        $date = new DateTime();
        file_put_contents($this->file_name, $date->format('Y-m-d H:i:s'));
        $last_date = $this->date_write_reader->readLastUpdatedTime();
        $this->assertEquals($date, $last_date);
    }

    public function testWriteLastUpdatedTime()
    {
        $date = new DateTime();
        $this->date_write_reader->writeLastUpdatedTime($date);
        $data = file_get_contents($this->file_name);
        $this->assertEquals($date, DateTime::createFromFormat('Y-m-d H:i:s', $data));
        unlink($this->file_name);
    }
}
