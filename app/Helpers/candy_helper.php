<?php

function encryptData($data)
{
    $key = "1234567890abcdef"; // 16 karakter
    $iv  = "abcdef1234567890"; // 16 karakter

    $encrypted = openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($encrypted);
}



function decryptData($data)
{
    $key = "1234567890abcdef"; // Harus sama dengan key saat enkripsi
    $iv  = "abcdef1234567890"; // Harus sama dengan IV saat enkripsi

    $decrypted = openssl_decrypt(base64_decode($data), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
}

function customEncrypt(string $data, string $secretKey): string
{
    $key = hash('sha256', $secretKey, true); // 32 bytes key
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivLength);

    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    if ($encrypted === false) {
        throw new Exception('Encryption failed.');
    }

    return base64_encode($iv . $encrypted); // Simpan IV + encrypted
}
function customDecrypt(string $encryptedData, string $secretKey): string|false
{
    $key = hash('sha256', $secretKey, true);
    $data = base64_decode($encryptedData, true);

    if ($data === false) {
        return false;
    }

    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);

    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
}
