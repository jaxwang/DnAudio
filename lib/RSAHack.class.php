<?php
/**
 * @Author: Jax Wang
 * @Date:   2018-09-07 00:59:58
 * @Last Modified by:   Jax Wang
 * @Last Modified time: 2018-09-07 01:32:24
 */

require './lib/phpseclib/Crypt/RSA.php';
require './lib/phpseclib/Math/BigInteger.php';

Class RSAHack{
    private $rsa_n = 'D28B9DAEBBBC2884F31981791EF959AC0AB1BB1987ADDE98EA6932CB0AB5DCFE592284D296F3A0FDB8962496597F4BF1142972F08E9982164896ADBAA05284EA56072A1E74D8D134570386466C36AEA4FFAB6BC2C1B911A1F1ADC5EF89BB1AA07EC14F540DD49C2EC3CA95C5D290E7C2ED418CA469F13C3AE69B9D06BE6B495D';
    private $rsa_e = '10001'; // 16进制

    public function __construct(){

    }
    public function encrypt(){
        $rsa = new Crypt_RSA();
        $rsa->loadKey(array(
            'n' => new Math_BigInteger($this->rsa_n, 16),
            'e' => new Math_BigInteger($this->rsa_e, 16)
        ));
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $encrypt_data = $rsa->encrypt('{"platform":0,"timestamp":' . time() . '}');
        return bin2hex($encrypt_data);
    }

}
