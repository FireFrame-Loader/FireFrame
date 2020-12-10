<?php

namespace general;

use phpseclib\Crypt\RSA;

define('CRYPT_RSA_PKCS15_COMPAT', true);

$private_key = "-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEA1LzoJpqhniHZNul4HzGnI60K3L98CqJqgMqioGdUpsAY6t68
30PrxAjshylzPidh8vOs33O/6ifpsucrbI9H3PJrepzMQ83rGL0udsHGzF5jcuRo
s+32Bja/zOEkdmh9bcPs2/k6Fsa3LWRcmtgJFrxfTHGYrAzOTkrd7e9nDH0OP67i
iglVen5Vxsyrxtd3VAmF+XTiAwdgdunP8afcmDcZeG4VmqFI5GxIw3KkqlLgql+F
v60xHEU0DPJS4/LcT5JY+rfZG/o89ncoogQyK6Ea/5XiFWQrJC7Mi+fhhZqPEV/9
qZ/EX+n20i3Hm/hgos/0gUVgJEDOEpmj/8sf8QIDAQABAoIBAQCPI4wQbXrRK3U7
vVtVodMT7hqg7TZI8X83qSBDTUNn71jUr4nlr42zGU4Mo5cm+tis38gxkXBP3Qsh
lOli4gU4ZORsW5QVCzhkLOZWgYuBdwDzDTxl1Hb1N2FWOSaPhT7f3DRCzi6HpK0b
nauxhCqZYaW9ibFwEi2+ACHTNoHfpAsUQMw5uALb3x4GiNE6oU50aivu76PeOW9/
gN1Qa9kCXkGp9Vye6aB6HxOlcbjgStLKrPgmFEuqYuSiHz4NLY9cW3Ytbvov1Lza
i8yLuzOUw5FgZHepiK64MMXQeu7Hq+Sg4lK8B98fKjL+4j7LQDTVqkEqTRiPlM9l
a48d2gABAoGBAO5wORgc3Op+5P8AH+j9CSiqeD/D+mUi6tYp6js81H5AsRqdNYCX
jnR4MXlzwDe1YX9TCowS0k8mr7BYP0Z74crRCbZBycemz/puRevGE/w3LoqBQDPV
r7nlgB/t7ms78LDriOYLUHyvuAi/cIITvFjb2JJqsWZMk+U0+qdUHdxBAoGBAORo
GXUASK0A+ZtLdx6KuFU5Zrq7E9R7QCExOQEZdpx+llIJWH9YH3dTtbbDOAI3bezr
lTzQ0sqPIv0ssELEVIZ505TsXDB9QKpK1fr1KLDYoebbSCpEKVc7J8/oR90xW5We
7RX0uiExEd74+pdhmUoogRjF3kUZ64WxzpzBXBexAoGBAIS3FjBjsFsdcly1Nw25
+eUWrYPgk3jDrK6z+dorC6OSYoGLy4Hd2b7eP7t4QB9B6Bi0ogRBXaoMwHGJTP7w
aUc4fw1hJOzp3o1n36dSQ0F5fYA+XPv9DqiMI3qiNridTmVLVJGm7o/YRrknxXnB
fm5/P9aPaFqmeRN1H6cPXOeBAoGBAMrX0FPiDqjmBsZtvLo5A/9b3OnUnoXDd26C
4lchn/7XRPRSLG/beQZmJyjKMoF1bIBNr9m0sp8Fg1NITrjc1xweMVM+nZjKg5U4
pNQcyShVG2OENpCiu/wmIvM3HCtKXEWCQJeRIYO8qlxUzWeHW7VKR2wSvZSssken
YxA/gGPhAoGAZJD8F5+ZgJlLEKawGCkmCd+3QGMmD5YFGmY+wxKpqHGJdAnwv7NA
SLWmauCd7Tg2Vt7SkMDZKbHWrW6qshq/iIVUYvdPrFbC7amPQazR0Hv5bJ7Hbj1x
TjpDwTMFmmNT7vOBD0XAKR+ZqvSKL8460D/bhguCLaE/kYwXMLX6sMI=
-----END RSA PRIVATE KEY-----";



function decrypt_rsa($cipher_text){
    $rsa = new RSA();

    $rsa->loadKey($private_key);

    return $rsa->decrypt($cipher_text);
}

function encrypt_aes($plaintext, $password,$iv = "_FireFrame_") {
    $method = "AES-256-CBC";
    $ciphertext = openssl_encrypt($plaintext, $method, $password, OPENSSL_RAW_DATA, $iv);
   return base64_encode($ciphertext);
}
  
function decrypt_aes($ciphertext, $password, $iv = "_FireFrame_") {
      $method = "AES-256-CBC";
      return openssl_decrypt(base64_decode($ciphertext), $method, $password, OPENSSL_RAW_DATA,  $iv);	
}
  

?>