<?php

namespace App\Security;

use App\Helpers\CryptHack;
use App\Helpers\CryptoHelper;
use Nette\SmartObject;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

/**
 * Description of CryptoService
 *
 * @author Vojta
 */
class CryptoService
{
    use SmartObject;

    const CIPHER = 'aes-256-cbc';

    private $apiKey;
    private $iv;

    const KEY_BYTE_SIZE = 64;
    const IV_BYTE_SIZE = 16;

    public function __construct($apiKey, $iv)
    {
        $this->apiKey = $apiKey;
        $this->iv = base64_decode($iv);
    }

    /**
     * @param $plaintext
     *
     * @return string
     */
    public function encrypt($plaintext)
    {
        $data = openssl_encrypt($plaintext, self::CIPHER, $this->apiKey, OPENSSL_RAW_DATA, $this->iv);
        return base64_encode(CryptoHelper::binToHex($data));
    }

    /**
     * @param $encryptedtext
     *
     * @return string
     */
    public function decrypt($encryptedtext)
    {
        $endCheckHack = new DateTime('2021-03-01');
        $current = new DateTime();
        if ($current < $endCheckHack && array_key_exists($encryptedtext, CryptHack::HACK)) {
            return (int)CryptHack::HACK[$encryptedtext];
        }

        $decoded = CryptoHelper::hexToBin(base64_decode($encryptedtext));
        return openssl_decrypt($decoded, self::CIPHER, $this->apiKey, OPENSSL_RAW_DATA, $this->iv);
    }

    public function generateKey(int $byteLength = self::KEY_BYTE_SIZE)
    {
        $encryption_key = openssl_random_pseudo_bytes($byteLength);
        return base64_encode($encryption_key);
    }

    public function generateIV(int $byteLength = self::IV_BYTE_SIZE)
    {
        return base64_encode(openssl_random_pseudo_bytes($byteLength));
    }

}
