<?php

namespace App\Service\Twig;

use App\Service\Twig\Common\StringAppRuntime;
use App\Service\Twig\Entity\ArticleAppRuntime;
use App\Service\Twig\Entity\AuthorAppRuntime;
use App\Service\Twig\Entity\BoxAppRuntime;
use App\Service\Twig\Entity\CategoryAppRuntime;
use App\Service\Twig\Entity\LastAppRuntime;
use App\Service\Twig\Entity\SearchAppRuntime;
use App\Service\Twig\Entity\WorkflowAppRuntime;

/**
 * Class AppExtension
 * @package App\Service\Twig
 *
 * Custom twig filter
 * @url https://symfony.com/doc/current/templating/twig_extension.html
 */
class AppExtension extends \Twig_Extension
{
    /**
     * Twig calls :
     * {{ string  | maxLen(47) }}
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('maxLen', [StringAppRuntime::class, 'maxLength']),
        ];
    }

    /**
     * @return array
     *
     * {{ boxLink(box) | raw }}
     * {{ boxImageLink(box) | raw }}
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('getUri', [AppRuntime::class, 'getUri']),
            # Box
            new \Twig_Function('boxLink', [BoxAppRuntime::class, 'boxLink']),
            new \Twig_Function('boxImage', [BoxAppRuntime::class, 'boxImage']),
            new \Twig_Function('boxImageLink', [BoxAppRuntime::class, 'boxImageLink']),
            new \Twig_Function('boxesLink', [BoxAppRuntime::class, 'boxesLink']),
            # Workflow
            new \Twig_Function('workflowLinks', [WorkflowAppRuntime::class, 'workflowLinks']),

        ];
    }
}
