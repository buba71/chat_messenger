<?php

namespace App\Mercure;

use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Builder;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGenerator
{
    public function __construct(private string $secret) {}
    
    public function generate(): Cookie
    {
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => ['*']])
            ->getToken(new Sha256(), InMemory::plainText($this->secret))->toString();

        return Cookie::create('mercureAuthorization', $token, 0, '/.well-known/mercure', null, null, true);
    }
}