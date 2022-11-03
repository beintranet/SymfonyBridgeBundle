<?php

namespace LightSaml\SymfonyBridgeBundle\Bridge\Store\Request;

use LightSaml\Store\Request\AbstractRequestStateArrayStore;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RequestStateSessionStore extends AbstractRequestStateArrayStore
{
    protected RequestStack $requestStack;
    protected string $providerId;
    protected string $prefix;

    public function __construct(
        RequestStack $requestStack,
        string $providerId,
        string $prefix = 'saml_request_state_'
    ) {

        $this->requestStack = $requestStack;
        $this->providerId = $providerId;
        $this->prefix = $prefix;
    }

    protected function getKey(): string
    {
        return sprintf('%s_%s', $this->providerId, $this->prefix);
    }

    protected function getArray(): array
    {
        return $this->getSession()->get($this->getKey(), []);
    }

    protected function setArray(array $arr)
    {
        $this->getSession()->set($this->getKey(), $arr);
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
