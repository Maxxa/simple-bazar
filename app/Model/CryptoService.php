<?php

namespace App\Collegas\Security;

use App\Helpers\CryptoHelper;
use Nette\Object;

/**
 * Description of CryptoService
 *
 * @author Vojta
 */
class CryptoService extends Object
{

    private $apiKey;

    const KEY_BYTE_SIZE = 16;

    // the default options to use.
    // most of the time you won't need to change these
    // unless your client doesn't support these.
    private $options = array(
        'method' => MCRYPT_RIJNDAEL_256,
        'mode' => MCRYPT_MODE_ECB,
        'rand_source' => MCRYPT_RAND,
    );

    public function __construct($apiKey)
    {
        dump(base64_encode($this->generateKey()));
        die();
        $this->apiKey = base64_decode($apiKey);
    }

    /**
     * @return string
     */
    public function generateKey()
    {
        return openssl_random_pseudo_bytes(self::KEY_BYTE_SIZE);
    }

    /**
     * encrypt the pain text string.
     * the returned value is a raw binary string, so if you plan to pass it over the
     * wire it makes sense to base64 encode it.
     * @param $plaintext
     * @return string
     */
    public function encrypt($plaintext)
    {
        $encrypted = mcrypt_encrypt(
            $this->options['method'],
            $this->apiKey,
            $plaintext,
            $this->options['mode'],
            mcrypt_create_iv(
                mcrypt_get_iv_size(
                    $this->options['method'],
                    $this->options['mode']
                ),
                $this->options['rand_source']
            )
        );
        return base64_encode(CryptoHelper::binToHex($encrypted));
    }

    // decrypt the data.
    // note: you may lose "\0" at the end of the original string because the php implementation of
    // mcrypt_decrypt pads the end of the string with this character to preserve a given block size.
    // (sloppy, but I don't know of a work-around.
    /**
     * @param $encryptedtext
     * @return string
     */
    public function decrypt($encryptedtext)
    {
        $binaryText = CryptoHelper::hexToBin(base64_decode($encryptedtext));
        $result = rtrim(mcrypt_decrypt(
            $this->options['method'],
            $this->apiKey,
            $binaryText,
            $this->options['mode'],
            mcrypt_create_iv(
                mcrypt_get_iv_size(
                    $this->options['method'],
                    $this->options['mode']
                ),
                $this->options['rand_source']
            )
        ), "\0");
        return $result;
    }

}
