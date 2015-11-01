<?php

class SecureTokenTest extends PHPUnit_Framework_TestCase
{
    public function testEncryptDecrypt()
    {
        $token = new SecureToken('deadbeefdeadbeefdeadbeefdeadbeef');
        $message = 'Грузите апельсины бочками';
        $encrypted = $token->encrypt($message);
        $this->assertNotEquals($message, $encrypted);
        $this->assertEquals($message, $token->decrypt($encrypted));
        $this->assertNull($token->decrypt('some junk stuff'));
    }
}
