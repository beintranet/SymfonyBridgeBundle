<?php

declare(strict_types=1);

namespace LightSaml\SymfonyBridgeBundle\Tests\Bridge\Store\Request;

use LightSaml\State\Request\RequestState;
use LightSaml\SymfonyBridgeBundle\Bridge\Store\Request\RequestStateSessionStore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RequestStateSessionStoreTest extends TestCase
{
    public function testSetsToSession()
    {
        $requestStack = $this->getRequestStack();

        /** @var MockObject|SessionInterface $sessionMock */
        $sessionMock = $requestStack->getSession();

        $store = new RequestStateSessionStore(
            $requestStack,
            'main'
        );

        $requestState = new RequestState('aaa');

        $sessionMock->method('get')->willReturn([]);
        $sessionMock->method('set')
            ->with('main_saml_request_state_', $this->isType('array'))
            ->willReturnCallback(function ($name, $value) use ($requestState) {
                $this->assertArrayHasKey('aaa', $value);
                $this->assertSame($requestState, $value['aaa']);
            });

        $store->set($requestState);
    }

    public function testGetsFromSession(): void
    {
        $requestStack = $this->getRequestStack();
        /** @var MockObject|SessionInterface $sessionMock */
        $sessionMock = $requestStack->getSession();

        $store = new RequestStateSessionStore(
            $requestStack,
            'main'
        );

        $id = 'aaa';
        $sessionMock->method('get')
            ->with('main_saml_request_state_')
            ->willReturn([$id => $expected = new RequestState($id)]);

        $actual = $store->get($id);

        $this->assertSame($expected, $actual);
    }

    public function testRemove(): void
    {
        $requestStack = $this->getRequestStack();
        /** @var MockObject|SessionInterface $sessionMock */
        $sessionMock = $requestStack->getSession();

        $store = new RequestStateSessionStore(
            $requestStack,
            'main'
        );

        $id = 'aaa';
        $sessionMock->expects($this->once())
            ->method('get')
            ->willReturn([$id => $expected = new RequestState($id)]);
        $sessionMock->expects($this->once())
            ->method('set')
            ->with('main_saml_request_state_', []);

        $store->remove($id);
    }

    public function testClear(): void
    {
        $requestStack = $this->getRequestStack();
        /** @var MockObject|SessionInterface $sessionMock */
        $sessionMock = $requestStack->getSession();

        $store = new RequestStateSessionStore(
            $requestStack,
            'main'
        );

        $sessionMock->expects($this->once())
            ->method('set')
            ->with('main_saml_request_state_', []);

        $store->clear();
    }

    private function getRequestStack(): RequestStack
    {
        $mockSession = $this->getMockBuilder(SessionInterface::class)->getMock();
        $request = new Request();
        $request->setSession($mockSession);

        $stack = new RequestStack();
        $stack->push($request);

        return $stack;
    }
}
