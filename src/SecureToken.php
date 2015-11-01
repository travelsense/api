<?php

class SecureToken
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $cipher;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var string
     */
    private $ivSize;

    /**
     * SecureToken constructor.
     * @param string $key
     * @param string $cipher
     * @param string $mode
     */
    public function __construct($key, $cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC)
    {
        $this->key = pack('H*', $key);
        $this->cipher = $cipher;
        $this->mode = $mode;
        $this->ivSize = mcrypt_get_iv_size($cipher, $mode);
    }

    /**
     * Encrypt data
     * @param string $data
     * @return string Encrypted message
     */
    public function encrypt($data)
    {
        $container = [$data]; // to check integrity on decryption
        $iv = mcrypt_create_iv($this->ivSize, MCRYPT_RAND);
        $encrypted = mcrypt_encrypt($this->cipher, $this->key, serialize($container), $this->mode, $iv);
        return base64_encode($iv.$encrypted);
    }

    /**
     * Decrypt message
     * @param $encrypted
     * @return string|null
     */
    public function decrypt($encrypted)
    {
        $decoded = base64_decode($encrypted);
        $iv = substr($decoded, 0, $this->ivSize);
        if (strlen($iv) < $this->ivSize) {
            return null;
        }
        $ciphertext = substr($decoded, $this->ivSize);
        $serialized = mcrypt_decrypt($this->cipher, $this->key, $ciphertext, $this->mode, $iv);
        $container = unserialize($serialized);
        return is_array($container) ? $container[0] : null;
    }

}