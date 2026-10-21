<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\AccessMapInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Garantit que CHAQUE route sous /admin est bien fermée :
 *
 *  1. par la règle console access_control (toute route /admin matche
 *     { path: ^/admin, roles: ROLE_ADMIN }) : la première règle matchée porte
 *     ROLE_ADMIN ou ROLE_SUPER_ADMIN — anonymes → redirection login,
 *     membres simples → 403 ;
 *  2. par un attribut #[IsGranted] sur le controller : c'est le vrai garde,
 *     la matrice READ/WRITE par module (ou ROLE_SUPER_ADMIN sur les 4 pages
 *     sensibles : administrators, logs, healthcheck, techletter/members).
 *     Seule entrée exception : le dashboard (admin_home), marqueur
 *     #[IsGranted('ROLE_ADMIN')] assumé car tout profil console y accède ;
 *     les redirections legacy d'authentification (login/logout/password)
 *     restent publiques par design.
 *
 * Pour chaque route, on fait passer des tokens de test (anonyme, adhérent
 * expiré, ROLE_ADMIN, chaque ROLE_*_READER/WRITER, ROLE_SUPER_ADMIN) et on
 * vérifie l'accès normalisé (règle console ET #[IsGranted], WRITE ⇒ READ).
 */
final class AdminAccessControlTest extends KernelTestCase
{
    /** Rôle légal sur la première règle access_control matchée pour une route /admin (la règle console). */
    private const array CONSOLE_ROLES = ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];

    /** Profils admin testés contre les deux barrières ; tout le reste (ROLE_USER, ROLE_MEMBER_EXPIRED…) doit être refusé. */
    private const array ADMIN_ROLES = [
        'ROLE_ADMIN',
        'ROLE_SUPER_ADMIN',
        'ROLE_MEMBRES_READER', 'ROLE_MEMBRES_WRITER',
        'ROLE_VEILLE_READER', 'ROLE_VEILLE_WRITER',
        'ROLE_SITE_READER', 'ROLE_SITE_WRITER',
        'ROLE_APERO_READER', 'ROLE_APERO_WRITER',
        'ROLE_EVENT_READER', 'ROLE_EVENT_WRITER',
        'ROLE_COMPTA_READER', 'ROLE_COMPTA_WRITER',
        'ROLE_AG_READER', 'ROLE_AG_WRITER',
        'ROLE_PLANETE_READER', 'ROLE_PLANETE_WRITER',
        'ROLE_ANTENNES_READER', 'ROLE_ANTENNES_WRITER',
    ];

    /** WRITE ⇒ READ attendu de la hiérarchie configurée dans config/packages/security.yaml. */
    private const array WRITE_TO_READ = [
        'ROLE_MEMBRES_WRITER' => 'ROLE_MEMBRES_READER',
        'ROLE_VEILLE_WRITER' => 'ROLE_VEILLE_READER',
        'ROLE_SITE_WRITER' => 'ROLE_SITE_READER',
        'ROLE_APERO_WRITER' => 'ROLE_APERO_READER',
        'ROLE_EVENT_WRITER' => 'ROLE_EVENT_READER',
        'ROLE_COMPTA_WRITER' => 'ROLE_COMPTA_READER',
        'ROLE_AG_WRITER' => 'ROLE_AG_READER',
        'ROLE_PLANETE_WRITER' => 'ROLE_PLANETE_READER',
        'ROLE_ANTENNES_WRITER' => 'ROLE_ANTENNES_READER',
    ];

    private const string MEMBER_ROLE = 'ROLE_MEMBER_EXPIRED';

    private const array READER_ROLES = [
        'ROLE_MEMBRES_READER',
        'ROLE_VEILLE_READER',
        'ROLE_SITE_READER',
        'ROLE_APERO_READER',
        'ROLE_EVENT_READER',
        'ROLE_COMPTA_READER',
        'ROLE_AG_READER',
        'ROLE_PLANETE_READER',
        'ROLE_ANTENNES_READER',
    ];

    /** Rôles WRITE légaux en attribut controller (clés de WRITE_TO_READ). */
    private const array WRITER_ROLES = [
        'ROLE_MEMBRES_WRITER',
        'ROLE_VEILLE_WRITER',
        'ROLE_SITE_WRITER',
        'ROLE_APERO_WRITER',
        'ROLE_EVENT_WRITER',
        'ROLE_COMPTA_WRITER',
        'ROLE_AG_WRITER',
        'ROLE_PLANETE_WRITER',
        'ROLE_ANTENNES_WRITER',
    ];

    /** Entrée console : seule route autorisée à porter #[IsGranted('ROLE_ADMIN')] (marqueur, pas un garde fonctionnel). */
    private const string CONSOLE_ENTRY_ROUTE = 'admin_home';

    /** Redirections legacy /admin/(login|logout|password) → pages publiques : publiques par design, pas de garde en code. */
    private const array LEGACY_AUTH_ROUTES = ['auth_old_login', 'auth_old_logout', 'auth_old_password'];

    private static ?KernelInterface $sharedKernel = null;

    public static function tearDownAfterClass(): void
    {
        self::$sharedKernel = null;
        static::$kernel = null;
    }

    /** @return iterable<string, array{0: string, 1: Route}> */
    public static function provideAdminRoutes(): iterable
    {
        /** @var RouterInterface $router */
        $router = self::sharedContainer()->get('router');

        foreach ($router->getRouteCollection() as $name => $route) {
            if (!str_starts_with($route->getPath(), '/admin')) {
                continue;
            }

            yield $name => [$name, $route];
        }
    }

    #[DataProvider('provideAdminRoutes')]
    public function testRouteIsGated(string $routeName, Route $route): void
    {
        $container = self::sharedContainer();
        /** @var AccessMapInterface $accessMap */
        $accessMap = $container->get('security.access_map');
        /** @var AuthorizationCheckerInterface $checker */
        $checker = $container->get('security.authorization_checker');
        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = $container->get('security.token_storage');
        $path = $route->getPath();

        // ── access_control : la première règle matchée est la règle console.
        $request = $this->requestFor($path, $routeName);
        [$attributes] = $accessMap->getPatterns($request);
        self::assertNotEmpty($attributes, "La route {$routeName} ({$path}) n'est couverte par aucune règle console : elle n'est pas protégée par access_control (attendu : la règle ^/admin).");
        $mapRole = $attributes[0];
        if (in_array($routeName, self::LEGACY_AUTH_ROUTES, true)) {
            // Redirections legacy du domaine auth : publiques par design, pas de garde en code.
            self::assertSame('PUBLIC_ACCESS', $mapRole, "La route {$routeName} ({$path}) doit rester publique (redirection legacy vers une page d'authentification) ; premier rôle matché : '{$mapRole}'.");

            return;
        }
        self::assertContains(
            $mapRole,
            self::CONSOLE_ROLES,
            "La route {$routeName} ({$path}) n'est pas couverte par la règle console : le premier rôle matché est '{$mapRole}', attendu ROLE_ADMIN ou ROLE_SUPER_ADMIN.",
        );

        // ── #[IsGranted] : présent sur le controller, et c'est lui le vrai garde.
        $controllerRole = $this->controllerIsGrantedRole($route);
        self::assertNotNull($controllerRole, "Pas d'attribut #[IsGranted] sur le controller de la route {$routeName} ({$path}).");
        if ($routeName === self::CONSOLE_ENTRY_ROUTE) {
            self::assertSame(
                'ROLE_ADMIN',
                $controllerRole,
                "L'entrée console ({$routeName}, {$path}) doit porter le marqueur #[IsGranted('ROLE_ADMIN')] ; valeur '{$controllerRole}' inattendue.",
            );
        } else {
            self::assertTrue(
                in_array($controllerRole, self::READER_ROLES, true)
                || in_array($controllerRole, self::WRITER_ROLES, true)
                || $controllerRole === 'ROLE_SUPER_ADMIN',
                "Attribut #[IsGranted] '{$controllerRole}' interdit sur le controller de la route {$routeName} ({$path}) : la matrice READ/WRITE ou ROLE_SUPER_ADMIN, jamais ROLE_ADMIN seul (cela réouvrirait l'accès aux simples comptes ROLE_ADMIN).",
            );
        }

        // ── La hiérarchie (et elle seule) décide qui franchit l'attribut en code.
        $expectedEffects = self::expectedMatrix($mapRole, $controllerRole);
        foreach ($expectedEffects as $profile => $expected) {
            $granted = $this->checkWithToken($checker, $tokenStorage, $profile, $mapRole, $controllerRole);
            self::assertSame(
                $expected,
                $granted,
                "Accès sur {$routeName} ({$path}) : le profil {$profile} doit " . ($expected ? 'passer' : 'être refusé') . ".",
            );
        }
        $tokenStorage->setToken(null);
    }

    /**
     * Matrice : profil => accès attendu, tenant compte des deux barrières
     * (règle console access_control ET #[IsGranted] controllerRole), WRITE ⇒ READ.
     *
     * La règle console fait passer tout profil admin et bloque anonyme /
     * adhérent expiré ; la discrimination fine (READ/WRITE, SUPER_ADMIN)
     * vient de l'attribut en code.
     *
     * @return array<string, bool>
     */
    private static function expectedMatrix(string $mapRole, string $controllerRole): array
    {
        $matrix = [
            'anonymous' => false,
            'member (expired)' => false,
        ];

        foreach (self::ADMIN_ROLES as $role) {
            $matrix[$role] = self::satisfies($role, $mapRole) && self::satisfies($role, $controllerRole);
        }

        return $matrix;
    }

    /**
     * Le rôle candidat satisfait-il le rôle requis ? Grâce à la hiérarchie :
     * READ ⇒ ROLE_ADMIN (marqueur console), WRITE ⇒ READ, SUPER_ADMIN ⇒ tous les WRITE et READ.
     */
    private static function satisfies(string $candidate, string $required): bool
    {
        if ($candidate === 'ROLE_SUPER_ADMIN') {
            // ROLE_SUPER_ADMIN possède la console et tous les READ et WRITE.
            return $required === 'ROLE_SUPER_ADMIN'
                || $required === 'ROLE_ADMIN'
                || in_array($required, self::READER_ROLES, true)
                || in_array($required, self::WRITER_ROLES, true);
        }

        if ($required === 'ROLE_ADMIN') {
            // Tout rôle fonctionnel (READ/WRITE) implique ROLE_ADMIN ; ROLE_ADMIN seul n'ouvre rien de fonctionnel.
            return $candidate === 'ROLE_ADMIN'
                || in_array($candidate, self::READER_ROLES, true)
                || array_key_exists($candidate, self::WRITE_TO_READ);
        }

        return $candidate === $required || (self::WRITE_TO_READ[$candidate] ?? null) === $required;
    }

    private function checkWithToken(AuthorizationCheckerInterface $checker, TokenStorageInterface $tokenStorage, string $profile, string $mapRole, string $controllerRole): bool
    {
        $token = match ($profile) {
            'anonymous' => new UsernamePasswordToken(new InMemoryUser('anon.', null, ['IS_AUTHENTICATED_ANONYMOUSLY']), 'main', ['IS_AUTHENTICATED_ANONYMOUSLY']),
            'member (expired)' => new UsernamePasswordToken(new InMemoryUser('member', null, [self::MEMBER_ROLE]), 'main', [self::MEMBER_ROLE]),
            default => new UsernamePasswordToken(new InMemoryUser('u', null, [$profile]), 'main', [$profile]),
        };
        $tokenStorage->setToken($token);

        // Les deux barrières doivent être franchies simultanément :
        // la règle console access_control ET l'attribut #[IsGranted] du controller.
        return $checker->isGranted($mapRole) && $checker->isGranted($controllerRole);
    }

    private function controllerIsGrantedRole(Route $route): ?string
    {
        $controller = $route->getDefault('_controller');
        if (!is_string($controller)) {
            return null;
        }

        if (str_contains($controller, ':') && !str_contains($controller, '::')) {
            return null; // service:method, pas traité ici.
        }

        [$class, $method] = array_pad(explode('::', $controller, 2), 2, null);
        if (!class_exists($class)) {
            return null;
        }

        $reflected = new \ReflectionClass($class);
        $attribute = $reflected->getAttributes(IsGranted::class)[0] ?? null;
        if ($attribute === null && $method !== null) {
            $attribute = $reflected->getMethod($method)->getAttributes(IsGranted::class)[0] ?? null;
        }

        return $attribute === null ? null : $attribute->newInstance()->attribute;
    }

    private function requestFor(string $path, string $routeName): Request
    {
        $request = Request::create($path);
        $request->attributes->set('_route', $routeName);

        return $request;
    }

    private static function sharedContainer(): ContainerInterface
    {
        if (self::$sharedKernel === null) {
            self::$sharedKernel = self::createKernel();
            self::$sharedKernel->boot();
        }

        return self::$sharedKernel->getContainer()->get('test.service_container');
    }
}
