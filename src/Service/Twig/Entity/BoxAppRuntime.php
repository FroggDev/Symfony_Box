<?php

namespace App\Service\Twig\Entity;

use App\Common\Traits\Html\ATagGeneratorTrait;
use App\Common\Traits\Html\ImgTagGeneratorTrait;
use App\Common\Traits\String\MaxLengthTrait;
use App\Entity\Box;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class ArticleAppRuntime
 * @package App\Service\Twig\Entity
 */
class BoxAppRuntime
{

    use ATagGeneratorTrait;

    use MaxLengthTrait;

    use ImgTagGeneratorTrait;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Packages
     */
    public $asset;


    /**
     * AppRuntime constructor.
     * @param RouterInterface $router
     * @param Packages $asset
     *
     * @author Frogg <admin@frogg.fr>
     * @contributor  Sandy Pierre <sandy.pierre97@gmail.com>
     * Who found how to call the assets
     */
    public function __construct(RouterInterface $router, Packages $asset)
    {
        $this->router = $router;
        $this->asset = $asset;
    }

    /**
     * @param Box $box
     * @param int|null $size
     * @return string
     */
    public function boxLink(Box $box, int $size = null) : string
    {
        $linkText = $size ? $this->maxLength($box->getName(), $size) : $box->getName();

        return $this->getATag($this->getBoxHref($box), $linkText);
    }


    /**
     * @param Box $box
     * @return string
     */
    public function boxImageLink(Box $box) : string
    {
        return $this->getATag($this->getBoxHref($box), $this->boxImage($box));
    }


    /**
     * @param Box $box
     * @param string|null $class
     * @return string
     */
    public function boxImage(Box $box, string $class = null) : string
    {
        return $this->getImgTag(
            $this->asset->getUrl('images/box/' . $box->getFeaturedImage()),
            $box->getName(),
            $class
        );
    }


    /**
     * @param Box $box
     * @return string
     */
    private function getBoxHref(Box $box) : string
    {
        return $this->router->generate('box_workflow',['id' => $box->getId()],UrlGeneratorInterface::ABSOLUTE_URL );
    }

    /**
     * @param string $text
     * @param string|null $class
     * @param string|null $currentPage
     * @return string
     */
    public function boxesLink(
        string $text,
        string $class = null,
        string $currentPage = null
    ): string {
        return $this->getATag(
            $this->getBoxesHref($currentPage),
            $text,
            $class
        );
    }


    /**
     * @param string|null $currentPage
     * @return string
     */
    private function getBoxesHref(string $currentPage=null) : string
    {
        if ($currentPage) {
            $routeParams['currentPage'] = $currentPage;
        }

        return $this->router->generate('index_admin', $routeParams,UrlGeneratorInterface::ABSOLUTE_URL);
    }

}
