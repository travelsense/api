<?php
namespace Api;


use Api\Service\PdfGenerator;

class PdfGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $perm = ['a', 'b'];

        $mpdf = $this->getMockBuilder(\mPDF::class)
            ->getMock();

        $mpdf->expects($this->once())
            ->method('Output')
            ->with(null, 'S')
            ->willReturn('my pdf');

        $mpdf->expects($this->once())
            ->method('WriteHTML')
            ->with('my html');

        $mpdf->expects($this->once())
            ->method('SetProtection')
            ->with($perm, 'my_password', null, 512);

        $generator = new PdfGenerator($perm, 'my_password', 512);

        $this->assertEquals('my pdf', $generator->generate('my html', $mpdf));
    }

    public function testDefaultMpdf()
    {
        $generator = new PdfGenerator([], 'my_password', 512);
        $this->assertNotEmpty($generator->generate('my html'));
    }
}
