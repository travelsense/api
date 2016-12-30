<?php
namespace Api\Service\GeoCoder;

use PHPUnit_Framework_TestCase;

class DateWriteReaderTest extends PHPUnit_Framework_TestCase
{
    private $date_write_reader;
    private $file_name = __DIR__.'/last_update_test.txt';

    public function setUp()
    {
        $this->date_write_reader = new DateWriteReader($this->file_name);
    }

    public function testReadLastUpdatedTime()
    {
        $last_date = $this->date_write_reader->readLastUpdatedTime();
        $this->assertEquals(null, $last_date);

        $date = date('Y-m-d H:i:s');
        file_put_contents($this->file_name, $date);
        $last_date = $this->date_write_reader->readLastUpdatedTime();
        $this->assertEquals($date, $last_date);
    }

    public function testWriteLastUpdatedTime()
    {
        $date = date('Y-m-d H:i:s');
        $this->date_write_reader->writeLastUpdatedTime($date);
        $data = file_get_contents($this->file_name);
        $this->assertEquals($date, $data);
        unlink($this->file_name);
    }
}
