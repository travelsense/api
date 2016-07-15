<?php
namespace Api;

use LazyPDO\LazyPDO;
use PDOStatement;
use PDO;

class AbstractPDOMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromJoined()
    {
        $mapper_a = $this->getMockForAbstractClass('\\Api\\AbstractPDOMapper', [new LazyPDO('')]);
        $object_a = (object)[];
        $mapper_a->expects($this->once())
            ->method('create')
            ->with(['a' => 'a', 'b' => 'b0'])
            ->willReturn($object_a);

        $mapper_b = $this->getMockForAbstractClass('\\Api\\AbstractPDOMapper', [new LazyPDO('')]);
        $object_b = (object)[];
        $mapper_b->expects($this->once())
            ->method('create')
            ->with(['a' => 'a', 'b' => 'b1'])
            ->willReturn($object_b);
        
        $mapper = new class($mapper_a, $mapper_b) extends AbstractPDOMapper
        {

            private $a, $b;

            public function __construct(AbstractPDOMapper $a, AbstractPDOMapper $b)
            {
                $this->a = $a;
                $this->b = $b;
            }

            public function test()
            {
                $row = [
                    'a' => 'a',
                    'b' => ['b0', 'b1'],
                ];
                return $this->createFromJoined($row, $this->a, $this->b);
            }

            public function create(array $row)
            {
            }
        };
        $this->assertEquals([$object_a, $object_b], $mapper->test());
    }

    public function dataProvider()
    {
        return array(
            array(PDO::PARAM_INT, ['integer' => 1]),
            array(PDO::PARAM_BOOL, ['boolean' => true]),
            array(PDO::PARAM_NULL, ['NULL' => null]),
            array(PDO::PARAM_STR, ['string' => ""]),
        );
    }

    /**
     * @param string $expected_type
     * @param array $value
     * @dataProvider dataProvider
     */
    public function testBindValuesRightWay($expected_type, $value)
    {
        $arr_key = array_keys($value);
        $mapper = $this->getMockForAbstractClass('\\Api\\AbstractPDOMapper', [new LazyPDO('')]);
        $stmt = $this->getMockBuilder('PDOStatement')->getMock();
        $stmt->expects($this->once())->method('bindValue')->with( $arr_key[0], $value[$arr_key[0]], $expected_type);
        $mapper->bindValues($stmt, $value);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testBindValuesBadWay()
    {
        $mapper = $this->getMockForAbstractClass('\\Api\\AbstractPDOMapper', [new LazyPDO('')]);
        $statement = new PDOStatement();
        $object = (object)[];
        $values = [
            'object' => $object
        ];
        $mapper->bindValues($statement, $values);
    }
}
