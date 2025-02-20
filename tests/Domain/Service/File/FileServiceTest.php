<?php declare(strict_types=1);

namespace tests\Domain\Service\File;

use App\Domain\Entities\File;
use App\Domain\Service\File\Exception\FileNotFoundException;
use App\Domain\Service\File\FileService;
use tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FileServiceTest extends TestCase
{
    protected FileService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->getService(FileService::class);
    }

    protected function getTestFileUrl()
    {
        return 'https://loremflickr.com/800/600?t=' . time();
    }

    protected function getTestFile()
    {
        $from = $this->getTestFileUrl();
        $to = CACHE_DIR . '/' . md5('tmp' . time()) . '.jpg';

        file_put_contents($to, file_get_contents($from));

        return $to;
    }

    public function testCreateSuccessFromLocal(): void
    {
        $path = $this->getTestFile();
        $info = File::info($path);

        $file = $this->service->createFromPath($path);
        $this->assertInstanceOf(File::class, $file);
        $this->assertSame($info['name'], $file->getName());
        $this->assertSame($info['ext'], $file->getExt());
        $this->assertSame($info['type'], $file->getType());
        $this->assertSame($info['size'], $file->getSize());
        $this->assertTrue(file_exists($file->getInternalPath()));
        $this->assertTrue(!file_exists($path));
    }

    public function testCreateSuccessFromRemote(): void
    {
        $path = $this->getTestFileUrl();

        $file = $this->service->createFromPath($path);
        $this->assertInstanceOf(File::class, $file);
        $this->assertTrue(file_exists($file->getInternalPath()));
    }

    public function testCreateWithFileAlreadyExistent(): void
    {
        $file1 = $this->service->createFromPath($this->getTestFile());

        // create new file obj from previously processed file
        $file2 = $this->service->createFromPath($file1->getInternalPath());

        $this->assertInstanceOf(File::class, $file2);
        $this->assertSame($file1, $file2);
    }

    public function testReadSuccess(): void
    {
        $file = $this->service->createFromPath($this->getTestFile());

        $f1 = $this->service->read(['uuid' => $file->getUuid()]);
        $this->assertInstanceOf(File::class, $f1);
        $this->assertSame($file->getFileName(), $f1->getFileName());

        $f2 = $this->service->read(['hash' => $file->getHash()]);
        $this->assertInstanceOf(File::class, $f2);
        $this->assertSame($file->getFileName(), $f2->getFileName());
    }

    public function testReadWithFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);

        $this->service->read(['hash' => md5('test' . time())]);
    }

    public function testUpdateSuccess(): void
    {
        $file = $this->service->createFromPath($this->getTestFile());

        $data = [
            'name' => $this->getFaker()->userName,
        ];

        $page = $this->service->update($file, $data);
        $this->assertSame($data['name'], $page->getName());
    }

    public function testUpdateWithFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);

        $this->service->update(null);
    }

    public function testDeleteSuccess(): void
    {
        $file = $this->service->createFromPath($this->getTestFile());

        $result = $this->service->delete($file);

        $this->assertTrue($result);
    }

    public function testDeleteWithFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);

        $this->service->delete(null);
    }
}
