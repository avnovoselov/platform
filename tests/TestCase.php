<?php declare(strict_types=1);

namespace tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\ToolsException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\App;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private static App $app;

    private static ContainerInterface $container;

    protected EntityManager $em;

    public static function setUpBeforeClass(): void
    {
        $app = null;
        $_ENV['TEST'] = 1; // in test always true (!)

        require SRC_DIR . '/bootstrap.php';

        /**
         * from bootstrap
         *
         * @var \Slim\App $app
         */
        static::$app = $app;
        static::$container = static::$app->getContainer();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ToolsException
     */
    public function setUp(): void
    {
        $this->em = $em = static::$container->get(EntityManager::class);

        /*
         * for each test, we will use an empty database
         * delete the scheme and create it again
         */
        $schema = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schema->dropSchema($em->getMetadataFactory()->getAllMetadata());
        $schema->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    protected function getService($class): mixed
    {
        return static::$container->get($class);
    }

    /**
     * @return \Faker\Generator
     */
    protected function getFaker(): \Faker\Generator
    {
        static $faker;

        if (!$faker) {
            $faker = \Faker\Factory::create();
        }

        return $faker;
    }

    protected function createRequest(): \GuzzleHttp\Client
    {
        static $client;

        if (!$client) {
            $client = new \GuzzleHttp\Client([
                'base_uri' => 'http://nginx:80',
                'http_errors' => false,
                // 'debug' => true,
            ]);
        }

        return $client;
    }
}
