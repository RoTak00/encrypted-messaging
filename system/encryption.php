<?php
class Encryption
{
    private $registry = [];

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function __get($name)
    {

        if (isset($this->registry->registry[$name])) {
            return $this->registry->registry[$name];
        }
        return null;
    }

    public function generateSymmetricKey()
    {
        return sodium_crypto_secretbox_keygen();
    }

    public function urlencode($binkey)
    {
        return rtrim(strtr(base64_encode($binkey), '+/', '-_'), '=');
    }

    public function urldecode($binkey)
    {
        return base64_decode(strtr($binkey, '-_', '+/'));
    }

    public function encryptSymmetric($data, $key)
    {
        if (!is_string($data)) {
            $data = json_encode($data);
        }

        try {

            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $cipher = sodium_crypto_secretbox($data, $nonce, $key);
            return base64_encode($nonce . $cipher);
        } catch (SodiumException $e) {
            file_put_contents('resources/enc.log', "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function decryptSymmetric($data, $key)
    {
        $data = base64_decode($data);
        $nonce = mb_substr($data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $cipher = mb_substr($data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        try {
            $decrypted_data = sodium_crypto_secretbox_open($cipher, $nonce, $key);
            return json_decode($decrypted_data, true);
        } catch (SodiumException $e) {
            file_put_contents('resources/enc.log', "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }


    public function generateAsymmetricKeypair()
    {
        $keypair = sodium_crypto_box_keypair();

        $pub_key = sodium_crypto_box_publickey($keypair);
        $priv_key = sodium_crypto_box_secretkey($keypair);

        return [
            'public' => $pub_key,
            'private' => $priv_key
        ];
    }

    public function getKeypairFromKeys($public, $private, $encoded = false)
    {
        if ($encoded) {
            $public = $this->urldecode($public);
            $private = $this->urldecode($private);
        }

        return sodium_crypto_box_keypair_from_secretkey_and_publickey($private, $public);
    }

    public function encryptAsymmetricWithPublic($data, $public_key, $encoded = false)
    {
        if ($encoded) {
            $public_key = $this->urldecode($public_key);
        }

        $cipher = sodium_crypto_box_seal($data, $public_key);

        return base64_encode($cipher);
    }


    public function decryptAsymmetric($data, $keypair)
    {
        $cipher = base64_decode($data);

        try {
            return sodium_crypto_box_seal_open($cipher, $keypair);
        } catch (SodiumException $e) {
            file_put_contents('resources/enc.log', "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    function generate_uuid_v4()
    {
        $data = random_bytes(16);

        // Set version to 0100 (UUIDv4)
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // Set bits 6-7 to 10 (variant DCE 1.1)
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }



}