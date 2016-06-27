<?php
namespace Api;

use LazyPDO\LazyPDO;

class AbstractPDOMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromJoined()
    {
        $mapperA = $this->getMockForAbstractClass('\\Api\\AbstractPDOMapper', [new LazyPDO('')]);
        $objectA = (object)[];
        $mapperA->expects($this->once())
            ->method('create')
            ->with(['a' => 'a', 'b' => 'b0'])
            ->willReturn($objectA);

        $mapperB = $this->getMockForAbstractClass('\\Api\\AbstractPDOMapper', [new LazyPDO('')]);
        $objectB = (object)[];
        $mapperB->expects($this->once())
            ->method('create')
            ->with(['a' => 'a', 'b' => 'b1'])
            ->willReturn($objectB);

        $mapper = new class($mapperA, $mapperB) extends AbstractPDOMapper
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
        $this->assertEquals([$objectA, $objectB], $mapper->test());
    }

}
