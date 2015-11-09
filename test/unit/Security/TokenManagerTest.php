<?php

namespace Security;

class TokenManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testEncryptDecrypt()
    {
        $token = new TokenManager('deadbeefdeadbeefdeadbeefdeadbeef');
        $message = 'Грузите апельсины бочками';
        $encrypted = $token->encrypt($message);
        $this->assertNotEquals($message, $encrypted);
        $this->assertEquals($message, $token->decrypt($encrypted));
        $this->assertNull($token->decrypt('some junk stuff'));
    }
}
