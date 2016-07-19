<?php
namespace Api\JSON;

use Api\Exception\ApiException;
use PHPUnit_Framework_TestCase;

class DataObjectTest extends PHPUnit_Framework_TestCase
{
    public function testExists()
    {
        $data = (object)[
            'a_string' => 'zzz',
            'a_null'   => null,
            'a_false'  => false,
        ];
        $json = new DataObject($data);
        $this->assertTrue($json->has('a_string'));
        $this->assertTrue($json->has('a_null'));
        $this->assertTrue($json->has('a_false'));
        $this->assertFalse($json->has('x'));
    }

    public function testGetRootObject()
    {
        $data = (object)['a' => 'b'];
        $json = new DataObject($data);
        $this->assertEquals($data, $json->getRootObject());
    }

    public function getPositiveTestData()
    {
        $test = $this;
        $cb = function ($val) use ($test) {
            $test->assertEquals('b', $val);
            return false;
        };
        return [
            [['a' => 'b'], 'a', null, null, 'b'],
            [['a' => 'b'], 'a', 'string', null, 'b'],
            [['a' => 'b'], 'a', ['boolean', 'string'], null, 'b'],
            [['a' => true], 'a', ['boolean', 'string'], null, true],
            [['a' => 'b'], 'a', ['boolean', 'string'], '/a|b/', 'b'],
            [['a' => 'b'], 'a', ['boolean', 'string'], $cb, 'b'],
        ];
    }

    public function getNegativeTestData()
    {
        $test = $this;
        $cb = function ($val) use ($test) {
            $test->assertEquals('b', $val);
            return 'OMG!';
        };
        return [
            [['a' => 'b'], 'a', ['boolean', 'string'], '/x/', 'Property a does not match /x/',],
            [['a' => 'b'], 'a', 'boolean', null, 'Property a is of type string, expected type(s): boolean',],
            [['a' => 'b'], 'a', ['int', 'float'], '/x/', 'Property a is of type string, expected type(s): int, float',],
            [['a' => 42], 'a', 'string', '/x/', 'Property a is of type integer, expected type(s): string',],
            [['a' => 'b'], 'a', ['boolean', 'string'], $cb, 'Property a is invalid: OMG!'],
            [['a' => 'b'], 'foo', ['boolean', 'string'], null, 'Property does not exist: foo'],
        ];
    }

    /**
     * @dataProvider getPositiveTestData
     * @param $json
     * @param $property
     * @param $type
     * @param $constraint
     * @param $result
     */
    public function testGetWithPositive($json, $property, $type, $constraint, $result)
    {
        $data = new DataObject((object)$json);
        $this->assertEquals($result, $data->get($property, $type, $constraint));
    }

    /**
     * @dataProvider getNegativeTestData
     * @param $json
     * @param $property
     * @param $type
     * @param $constraint
     * @param $exception
     */
    public function testGetWithException($json, $property, $type, $constraint, $exception)
    {
        $data = new DataObject((object)$json);
        try {
            $data->get($property, $type, $constraint);
            $this->fail('Exception expected');
        } catch (ApiException $e) {
            $this->assertEquals($exception, $e->getMessage());
            $this->assertEquals(ApiException::VALIDATION, $e->getCode());
        }
    }

    public function testGetString()
    {
        $data = new DataObject((object)['foo' => 'barx']);

        $this->assertEquals('barx', $data->getString('foo', '/x/'));

    }

    /**
     * @expectedException \Api\Exception\ApiException
     */
    public function testGetArrayOfException()
    {
        $data = new DataObject((object)['a' => ['b' => 'c']]);
        $data->getArrayOf('boolean', 'a');
    }

    public function testGetArrayOfHappyPath()
    {
        $data = new DataObject((object)['a' => ['b' => 'c']]);
        $this->assertEquals(['b' => 'c'], $data->getArrayOf('string', 'a'));
    }
}
