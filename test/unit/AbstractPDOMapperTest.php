<?php
namespace Api;

use LazyPDO\LazyPDO;

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

}
