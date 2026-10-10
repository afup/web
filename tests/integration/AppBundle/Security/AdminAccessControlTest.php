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
 *  1. par la première règle access_control matchée, avec un rôle de la matrice
 *     READ/WRITE ou ROLE_SUPER_ADMIN (jamais l'ancien filet ROLE_MEMBER_EXPIRED,
 *     jamais le role no-access ROLE_NO_ACCESS du default-deny) ;
 *  2. par un attribut #[IsGranted] cohérent avec access_control sur le
 *     controller (2e barrière en code).
 *
 * Pour chaque route, on fait passer des tokens de test (anonyme, adhérent,
 * ROLE_ADMIN, chaque ROLE_*_READER/WRITER, ROLE_SUPER_ADMIN) et on vérifie
 * l'accès normalisé (access_control ET #[IsGranted], WRITE ⇒ READ).
 */
final class AdminAccessControlTest extends KernelTestCase
{
    /** Rôles légaux pour couvrir une route /admin ; tout le reste (ROLE_NO_ACCESS, ROLE_USER…) est incorrect. */
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

        // ── access_control : la première règle matchée doit donner un rôle légal.
        $request = $this->requestFor($path, $routeName);
        $patterns = $accessMap->getPatterns($request);
        self::assertNotNull(
            $patterns,
            "La route {$routeName} ({$path}) ne matche aucune règle access_control : elle tombe dans le default-deny ROLE_NO_ACCESS → à rattraper dans config/packages/security.yaml",
        );
        [, $attributes] = $patterns;
        self::assertNotEmpty($attributes, "La première règle matchée pour {$routeName} ({$path}) ne porte aucun rôle.");
        $mapRole = $attributes[0];
        self::assertContains(
            $mapRole,
            self::ADMIN_ROLES,
            "Rôle '{$mapRole}' interdit sur {$routeName} ({$path}) : uniquement la matrice READ/WRITE, ROLE_ADMIN ou ROLE_SUPER_ADMIN.",
        );

        // ── #[IsGranted] : présent sur le controller et cohérent avec access_control.
        $controllerRole = $this->controllerIsGrantedRole($route);
        self::assertNotNull($controllerRole, "Pas d'attribut #[IsGranted] sur le controller de la route {$routeName} ({$path}).");
        if ($mapRole !== $controllerRole) {
            $isWriteReadPair =
                (self::WRITE_TO_READ[$mapRole] ?? null) === $controllerRole
                // access_control plus « haut niveau » que le code : relâchement codifié interdit sauf READ/WRITE pair légitime.
                || (self::WRITE_TO_READ[$controllerRole] ?? null) === $mapRole;

            self::assertTrue(
                $isWriteReadPair,
                "Incohérence pour {$routeName} ({$path}) : access_control = {$mapRole}, #[IsGranted] = {$controllerRole}. Attendu : même rôle, ou pair WRITER (access_control) ⇄ READER (code).",
            );
        }

        // ── La matrice doit éliminer les profils légitimes « faibles ».
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
     * (access_control mapRole ET #[IsGranted] controllerRole), WRITE ⇒ READ.
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
     * Le rôle candidat satisfait-il l'attribut demandé ? Grâce à la
     * hiérarchie : WRITE ⇒ READ, READ ⇒ ROLE_ADMIN (marqueur console).
     */
    private static function satisfies(string $candidate, string $required): bool
    {
        if ($required === 'ROLE_SUPER_ADMIN') {
            return $candidate === 'ROLE_SUPER_ADMIN';
        }

        if ($required === 'ROLE_ADMIN') {
            // Tout rôle fonctionnel (READ/WRITE) implique ROLE_ADMIN ; ROLE_ADMIN seul ne suffit pour rien d'autre.
            return $candidate === 'ROLE_ADMIN'
                || in_array($candidate, self::READER_ROLES, true)
                || array_key_exists($candidate, self::WRITE_TO_READ)
                || $candidate === 'ROLE_SUPER_ADMIN';
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
        // access_control (règle YAML) ET attribut #[IsGranted] du controller.
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
        $attribute = $reflected->getAttributes(IsGranted::class)[0]
            ?? ($method !== null ? $reflected->getMethod($method)->getAttributes(IsGranted::class)[0] ?? null : null);

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
