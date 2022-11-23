<?php

declare(strict_types=1);

namespace LightSaml\SymfonyBridgeBundle\Bridge\Store\Sso;

use LightSaml\State\Sso\SsoState;
use LightSaml\Store\Sso\SsoStateStoreInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SsoStateSessionStore implements SsoStateStoreInterface
{
    protected RequestStack $requestStack;
    protected string $key;

    public function __construct(RequestStack $requestStack, string $key)
    {
        $this->requestStack = $requestStack;
        $this->key = $key;
    }

    public function get(): SsoState
    {
        $result = $this->getSession()->get($this->key);
        if (null == $result) {
            $result = new SsoState();
            $this->set($result);
        }

        return $result;

    }

    public function set(SsoState $ssoState): void
    {
        $ssoState->setLocalSessionId($this->getSession()->getId());
        $this->getSession()->set($this->key, $ssoState);
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
