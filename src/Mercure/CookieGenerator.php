<?php

namespace App\Mercure;

use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;

class CookieGenerator
{
    public function __construct(private string $secret) {}
    
    public function generate(string $username): string
    {
        return (new Builder(new JoseEncoder(), ChainedFormatter::default()))
            ->withClaim('mercure', ['subscribe' => [sprintf("/%s", $username)]])
            ->getToken(new Sha256(), InMemory::plainText($this->secret))->toString();
    }
}