<?php

declare(strict_types=1);

namespace LightSaml\SymfonyBridgeBundle\Tests\Bridge\Store\Sso;

use LightSaml\State\Sso\SsoState;
use LightSaml\SymfonyBridgeBundle\Bridge\Store\Sso\SsoStateSessionStore;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SsoStateSessionStoreTest extends TestCase
{
    private const SESSION_KEY = 'samlsso';

    public function testGetReturnsObjectCreatedByDefault(): void
    {
        $store = new SsoStateSessionStore($this->getRequestStack(), self::SESSION_KEY);
        self::assertInstanceOf(SsoState::class, $store->get());
    }

    public function testGetReturnsSetObject(): void
    {
        $store = new SsoStateSessionStore($this->getRequestStack(), self::SESSION_KEY);
        $state = new SsoState();
        $store->set($state);

        self::assertSame($state, $store->get());
    }

    private function getRequestStack(): RequestStack
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $stack = new RequestStack();
        $stack->push($request);

        return $stack;
    }
}
