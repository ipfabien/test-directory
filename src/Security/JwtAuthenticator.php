<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Security\JwtTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class JwtAuthenticator extends AbstractAuthenticator
{
    private JwtTokenService $jwtTokenService;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
    }

    public function supports(Request $request): ?bool
    {
        return 0 === strpos($request->getPathInfo(), '/api/');
    }

    public function authenticate(Request $request): Passport
    {
        $authorization = $request->headers->get('Authorization');

        if ($authorization === null || $authorization === '') {
            throw new AuthenticationException('Missing Authorization header.');
        }

        if (strpos($authorization, 'Bearer ') !== 0) {
            throw new AuthenticationException('Invalid Authorization header format.');
        }

        $token = trim(substr($authorization, 7));

        if ($token === '') {
            throw new AuthenticationException('Empty bearer token.');
        }

        try {
            $claims = $this->jwtTokenService->decode($token);
        } catch (\Throwable $exception) {
            throw new AuthenticationException('Invalid or expired token.', 0, $exception);
        }

        $subject = isset($claims['sub']) ? (string) $claims['sub'] : 'anonymous';

        return new SelfValidatingPassport(
            new UserBadge(
                $subject,
                static function (string $userIdentifier): User {
                    return new User($userIdentifier, null, ['ROLE_API']);
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // let the request continue
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['error' => $exception->getMessage() ?: 'Authentication failed'],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
