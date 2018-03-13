<?php
namespace App\Service\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class AppRuntime
 * @package App\Service\Twig
 */
class AppRuntime
{
    private $requestStack;

    /**
     * AppRuntime constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack=$requestStack;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        $stack = $this->requestStack->getMasterRequest();
        return $stack->getRequestUri();
    }
}
