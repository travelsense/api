<?php
namespace Api\Service;

class PdfGenerator
{
    const AS_STRING = 'S';

    const KEY_LENGTH_128 = 128;

    private $permissions = [];
    private $password = '';
    private $key_length;
    private $mpdf;

    public function __construct(
        \mPDF $mpdf,
        array $permissions,
        string $password,
        int $key_length = self::KEY_LENGTH_128
    ) {
        $this->mpdf = $mpdf;
        $this->permissions = $permissions;
        $this->password = $password;
        $this->key_length = $key_length;
    }

    public function generate(string $html): string
    {
        $this->mpdf->WriteHTML($html);
        $this->mpdf->SetProtection($this->permissions, $this->password, null, $this->key_length);
        return $this->mpdf->Output(null, self::AS_STRING);
    }
}
