<?php declare(strict_types=1);

namespace tests\API;

use App\Domain\Service\Parameter\ParameterService;
use tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CatalogCategoryAPITest extends TestCase
{
    private string $apikey;

    public function setUp(): void
    {
        parent::setUp();

        $parameters = $this->getService(ParameterService::class);
        $parameters->create(['key' => 'entity_access', 'value' => 'key']);
        $parameters->create(['key' => 'entity_keys', 'value' => $this->apikey = $this->getFaker()->word]);
    }

    public function testAPICreateSuccess(): void
    {
        $data = [
            'title' => $this->getFaker()->word,
            'description' => $this->getFaker()->text,
            'field1' => $this->getFaker()->word,
            'field2' => $this->getFaker()->word,
            'field3' => $this->getFaker()->word,
            'export' => 'api',
        ];
        $response = $this->createRequest()->put('/api/v1/catalog/category', [
            'headers' => ['key' => $this->apikey],
            'form_params' => $data,
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testAPIReadSuccess(): void
    {
        // create
        $data = [
            'title' => $this->getFaker()->word,
            'description' => $this->getFaker()->text,
            'field1' => $this->getFaker()->word,
            'field2' => $this->getFaker()->word,
            'field3' => $this->getFaker()->word,
            'export' => 'api',
        ];

        $response = $this->createRequest()->put('/api/v1/catalog/category', [
            'headers' => ['key' => $this->apikey],
            'form_params' => $data,
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $json = json_decode((string) $response->getBody(), true);

        // read
        $response = $this->createRequest()->get('/api/v1/catalog/category', [
            'headers' => ['key' => $this->apikey],
            'query' => [
                'uuid' => $json['data']['uuid'] ?? 'null',
            ],
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode((string) $response->getBody(), true);

        // check
        $this->assertSame($data['title'], $json['data']['title']);
        $this->assertSame($data['description'], $json['data']['description']);
        $this->assertSame($data['field1'], $json['data']['field1']);
        $this->assertSame($data['field2'], $json['data']['field2']);
        $this->assertSame($data['field3'], $json['data']['field3']);
        $this->assertSame($data['export'], $json['data']['export']);
    }

    public function testAPIUpdateSuccess(): void
    {
        // create
        $data = [
            'title' => $this->getFaker()->word,
            'description' => $this->getFaker()->text,
            'field1' => $this->getFaker()->word,
            'field2' => $this->getFaker()->word,
            'field3' => $this->getFaker()->word,
            'export' => 'api',
        ];

        $response = $this->createRequest()->put('/api/v1/catalog/category', [
            'headers' => ['key' => $this->apikey],
            'form_params' => $data,
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $json = json_decode((string) $response->getBody(), true);

        // update
        $data = [
            'title' => $this->getFaker()->word,
            'description' => $this->getFaker()->text,
            'field1' => $this->getFaker()->word,
            'field2' => $this->getFaker()->word,
            'field3' => $this->getFaker()->word,
            'export' => 'api',
        ];

        $response = $this->createRequest()->patch('/api/v1/catalog/category', [
            'headers' => ['key' => $this->apikey],
            'query' => [
                'uuid' => $json['data']['uuid'] ?? 'null',
            ],
            'form_params' => $data,
        ]);
        $this->assertEquals(202, $response->getStatusCode());

        // read
        $response = $this->createRequest()->get('/api/v1/catalog/category', [
            'headers' => ['key' => $this->apikey],
            'query' => [
                'uuid' => $json['data']['uuid'] ?? 'null',
            ],
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode((string) $response->getBody(), true);

        // check
        $this->assertSame($data['title'], $json['data']['title']);
        $this->assertSame($data['description'], $json['data']['description']);
        $this->assertSame($data['field1'], $json['data']['field1']);
        $this->assertSame($data['field2'], $json['data']['field2']);
        $this->assertSame($data['field3'], $json['data']['field3']);
        $this->assertSame($data['export'], $json['data']['export']);
    }

    public function testAPIDeleteSuccess(): void
    {
        // create
        $data = [
            'title' => $this->getFaker()->word,
            'description' => $this->getFaker()->text,
            'field1' => $this->getFaker()->word,
            'field2' => $this->getFaker()->word,
            'field3' => $this->getFaker()->word,
            'export' => 'api',
        ];

        $response = $this->createRequest()->put('/api/v1/catalog/category', [
            'headers' => ['key' => $this->apikey],
            'form_params' => $data,
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $json = json_decode((string) $response->getBody(), true);

        // delete
        $response = $this->createRequest()->delete('/api/v1/catalog/category', [
            'headers' => ['key' => $this->apikey],
            'query' => [
                'uuid' => $json['data']['uuid'] ?? 'null',
            ],
        ]);
        $this->assertEquals(410, $response->getStatusCode());
    }
}
