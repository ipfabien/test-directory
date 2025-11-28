<?php

declare(strict_types=1);

namespace App\Services\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class JwtTokenService
{
    private string $secret;

    private string $algorithm;

    public function __construct(string $secret, string $algorithm = 'HS256')
    {
        $this->secret    = $secret;
        $this->algorithm = $algorithm;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \UnexpectedValueException
     * @throws \DomainException
     */
    public function decode(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));

        /** @var array<string, mixed> $data */
        $data = (array) $decoded;

        return $data;
    }

    /**
     * @param array<string, mixed> $claims
     */
    public function encode(array $claims): string
    {
        return JWT::encode($claims, $this->secret, $this->algorithm);
    }
}
