<?php
namespace App\Cache;

use App\SiteConfig;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ProductCacheWarmer
 * @package App\cache
 */
class ProductCacheWarmer extends CacheWarmer
{

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        // TODO: Implement isOptional() method.
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $filename = __DIR__ . '/../../' . SiteConfig::PRODUCTFILE;

        try {
            $products = Yaml::parseFile($filename);

            $this->writeCacheFile(
                $cacheDir.'/'.SiteConfig::PRODUCTCACHEFILE,
                serialize($products['data'])
            );
        } catch (ParseException $e) {
            printf("Unable to parse the YAML file : $filename", $e->getMessage());
        }
    }
}
