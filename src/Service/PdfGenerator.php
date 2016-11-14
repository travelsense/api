<?php
namespace Api\Service;

use mPDF;

class PdfGenerator
{
    const AS_STRING = 'S';

    const KEY_LENGTH_128 = 128;

    private $permissions = [];
    private $password = '';
    private $key_length;

    public function __construct(array $permissions, string $password, int $key_length = self::KEY_LENGTH_128)
    {
        $this->permissions = $permissions;
        $this->password = $password;
        $this->key_length = $key_length;
    }

    public function generate(string $html, mPDF $mpdf = null): string
    {
        $mpdf = $mpdf ?: new mPDF();
        $mpdf->WriteHTML($html);
        $mpdf->SetProtection($this->permissions, $this->password, null, $this->key_length);
        return $mpdf->Output(null, self::AS_STRING);
    }
}
