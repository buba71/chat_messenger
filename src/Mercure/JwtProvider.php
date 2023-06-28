<?php

namespace App\Mercure;

use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Builder;

class JwtProvider
{
    public function __construct(private string $secret) {}

    public function __invoke()
    {
        return (new Builder())
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken(new Sha256(), InMemory::plainText($this->secret));
    }
}