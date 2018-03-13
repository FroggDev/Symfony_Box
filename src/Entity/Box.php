<?php
namespace App\Entity;

use App\Common\Traits\String\SlugifyTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoxRepository")
 * @UniqueEntity(fields={"name"},errorPath="name",message="This name is already in use")
 * @see https://symfony.com/doc/current/reference/constraints/UniqueEntity.html
 */
class Box
{

    use SlugifyTrait;

    ##########
    # Entity #
    ##########

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $workflowStatus;

    /**
     * @ORM\Column(type="string", length=150,nullable=true)
     */
    private $featuredImage;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @TODO
     */
    private $products;

    ###########
    # Methods #
    ###########

    /**
     * Box constructor.
     */
    public function __construct()
    {
        # initialize date creation
        $this->dateCreation = new \DateTime();
        # init product list
        $this->setProducts([]);
        # init product list
        $this->workflowStatus="box_creation";
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Box
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Box
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * @return string
     */
    public function getNameSlugified(): string
    {
        return $this->slugify($this->name);
    }

    /**
     * @return mixed
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     * @param mixed $workflowStatus
     * @return Box
     */
    public function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param mixed $products
     * @return Box
     */
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @return mixed (due to Symfony image upload)
     */
    public function getFeaturedImage()
    {
        return $this->featuredImage;
    }

    /**
     * @param mixed $featuredImage (due to Symfony image upload)
     * @return Box
     */
    public function setFeaturedImage($featuredImage): Box
    {
        $this->featuredImage = $featuredImage;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateCreation()
    {
        return $this->dateCreation->format('d/m/Y');
    }

    /**
     * @return null|string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Box
     */
    public function setDescription($description): Box
    {
        $this->description = $description;
        return $this;
    }

}