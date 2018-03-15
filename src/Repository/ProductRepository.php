<?php

namespace App\Repository;

use App\Entity\Product;
use App\SiteConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $kernel;

    private $products;

    private $caheFile;

    /**
     * ArticleProvider constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(RegistryInterface $registry, KernelInterface $kernel)
    {
        parent::__construct($registry, Product::class);
        $this->kernel = $kernel;
    }


    /**
     * @param int $id
     * @return Product|null
     */
    public function findOneByIdFromProvider(int $id): ?Product
    {
        if (!$this->products) {
            $this->findAllFromProvider();
        }

        foreach ($this->products as $product) {
            if ($product->getId() == $id) {
                return $product;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function findAllFromProvider(): array
    {
        # Optimization if already loaded
        if ($this->products) {
            return $this->products;
        }

        $this->caheFile = $this->kernel->getCacheDir() . '/' . SiteConfig::PRODUCTCACHEFILE;

        if (file_exists($this->caheFile)) {
            $this->getProductsFromCache();
        } else {
            $this->getProductsFromFile();
        }

        $this->products = $this->convertAllToProduct($this->products);

        return $this->products;
    }

    private function getProductsFromCache()
    {
        $this->products = unserialize(file_get_contents($this->caheFile));
    }

    private function getProductsFromFile()
    {
        $filename = __DIR__ . '/../../' . SiteConfig::PRODUCTFILE;

        try {
            $this->products = Yaml::parseFile($filename);
        } catch (ParseException $exception) {
            VarDumper::dump("Cannot read $filename  : " . $exception->getMessage());
        }
    }


    /**
     * @param iterable $tmpProducts
     * @return array
     */
    private function convertAllToProduct(iterable $tmpProducts): array
    {
        $products = [];
        foreach ($tmpProducts as $product) {
            $products[] = $this->convertToProduct($product);
        }
        return $products;
    }

    /**
     * @param iterable $tmpProduct
     * @return Product|null
     */
    private function convertToProduct(iterable $tmpProduct): ?Product
    {
        $product = new Product();
        return $product
            ->setId($tmpProduct['id'])
            ->setTitle($tmpProduct['title'])
            ->setDescription($tmpProduct['description'])
            ->setPrice($tmpProduct['price']);
    }
}
