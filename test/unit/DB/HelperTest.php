<?php
namespace Api\DB;

use Doctrine\DBAL\Statement;
use InvalidArgumentException;
use PDO;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * @var Helper
     */
    private $helper;

    public function setUp()
    {
        $this->helper = new Helper();
    }

    public function dataProvider()
    {
        return [
            [PDO::PARAM_INT, 'integer', 1],
            [PDO::PARAM_BOOL, 'boolean', true],
            [PDO::PARAM_NULL, 'NULL', null],
            [PDO::PARAM_STR, 'string', ""],
        ];
    }

    /**
     * @param string $expected_type
     * @param $placeholder
     * @param array $value
     * @dataProvider dataProvider
     */
    public function testBindValuesHappyPath(string $expected_type, string $placeholder, $value)
    {
        $stmt = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stmt->expects($this->once())->method('bindValue')->with($placeholder, $value, $expected_type);
        $this->helper->bindValues($stmt, [$placeholder => $value]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot bind value of type 'object' to placeholder 'object'
     */
    public function testBindValuesException()
    {
        $stmt = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $object = (object)[];
        $values = [
            'object' => $object
        ];
        $this->helper->bindValues($stmt, $values);
    }

    public function testNormalize()
    {
        $original = [
            'a_key' => 'a_val',
            'b_key' => [
                'b_val_0',
                'b_val_1',
            ]
        ];
        $expected_0 = [
            'a_key' => 'a_val',
            'b_key' => 'b_val_0',
        ];
        $expected_1 = [
            'a_key' => 'a_val',
            'b_key' => 'b_val_1',
        ];
        $this->assertEquals($expected_0, $this->helper->normalize($original, 0));
        $this->assertEquals($expected_1, $this->helper->normalize($original, 1));
    }
    public function testGenerateInExpression()
    {
        $params = [
            ':foo' => 'bar',
        ];
        $values = ['a', 'b', 'c',];
        $expr = $this->helper->generateInExpression($values, 'name', $params);
        $this->assertEquals('(:name_0, :name_1, :name_2)', $expr);
        $expected_params = [
            ':foo' => 'bar',
            ':name_0' => 'a',
            ':name_1' => 'b',
            ':name_2' => 'c',
        ];
        $this->assertEquals('(:name_0, :name_1, :name_2)', $expr);
        $this->assertEquals($expected_params, $params);
    }
}
