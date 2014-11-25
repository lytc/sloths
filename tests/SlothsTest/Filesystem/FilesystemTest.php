<?php

namespace SlothsTest\Filesystem;

use SlothsTest\TestCase;
use Sloths\Filesystem\Filesystem;

/**
 * @covers Sloths\Filesystem\Filesystem
 */
class FilesystemTest extends TestCase
{
    /**
     * @var \Sloths\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $tmpDir;

    public function setUp()
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
        $this->tmpDir = sys_get_temp_dir();
    }

    public function testMkdirRecursively()
    {
        $directory = $this->tmpDir . '/foo/' . microtime();

        $this->filesystem->mkdir($directory);

        $this->assertTrue(is_dir($directory));
    }

    public function testRemoveFile()
    {
        $file = $this->tmpDir . '/' . microtime() . '.txt';
        touch($file);

        $this->assertFileExists($file);

        $this->filesystem->remove($file);

        $this->assertFileNotExists($file);
    }

    public function testRemoveDirectoryRecursively()
    {
        $rootDir = $this->tmpDir . '/' . uniqid();
        $subDir = $rootDir . '/sub';

        mkdir($rootDir);
        mkdir($subDir);

        $f1 = $rootDir . '/f1.txt';
        $f2 = $subDir . '/f1.txt';

        touch($f1);
        touch($f2);

        $this->assertFileExists($f1);
        $this->assertFileExists($f2);

        $this->filesystem->remove($rootDir);

        $this->assertFalse(is_dir($subDir));
        $this->assertFalse(is_dir($rootDir));
    }

    public function testChmodFile()
    {
        $file = $this->tmpDir . '/' . uniqid();
        touch($file);

        $this->filesystem->chmod($file, 0755);
        $this->filesystem->clearStatCache($file);

        $this->assertSame(0755, $this->filesystem->getMode($file));
    }

    public function testChmodDirectoryRecursively()
    {
        $rootDirectory = $this->tmpDir . '/' . uniqid();
        $subDirectory = $rootDirectory . '/foo';
        $file = $subDirectory . '/f1.txt';

        mkdir($rootDirectory);
        mkdir($subDirectory);
        touch($file);

        $this->filesystem->chmod($rootDirectory, 0755);
        $this->filesystem->clearStatCache($rootDirectory);

        $this->assertSame(0755, $this->filesystem->getMode($rootDirectory));
        $this->assertSame(0755, $this->filesystem->getMode($subDirectory));
        $this->assertSame(0755, $this->filesystem->getMode($file));
    }

    public function testRename()
    {
        $file = $this->tmpDir . '/' . uniqid();
        $newName = $this->tmpDir . '/' . uniqid();

        touch($file);

        $this->filesystem->rename($file, $newName);

        $this->assertFileNotExists($file);
        $this->assertFileExists($newName);
    }

    public function testCopyDirectory()
    {
        $directory = $this->tmpDir . '/' . uniqid();
        mkdir($directory);

        $subDirectoryName = 'sub';
        mkdir($directory . '/' . $subDirectoryName);

        $fileName = 'file';
        touch($directory . '/' . $subDirectoryName . '/' . $fileName);

        $destDirectory = $this->tmpDir . '/' . uniqid();
        $this->filesystem->copyDir($directory, $destDirectory);

        $this->assertFileExists($destDirectory . '/' . $subDirectoryName . '/' . $fileName);
    }

    public function testCopyFile()
    {
        $source = $this->tmpDir . '/' . uniqid();
        $dest = $this->tmpDir . '/' . uniqid();

        touch($source);

        $this->filesystem->copy($source, $dest);

        $this->assertFileExists($source);
        $this->assertFileExists($dest);
    }

    public function testCopyWithParamIsDirectory()
    {
        $directory = $this->tmpDir . '/' . uniqid();
        mkdir($directory);

        $filesystem = $this->getMock('Sloths\Filesystem\Filesystem', ['copyDir']);
        $filesystem->expects($this->once())->method('copyDir')->with($directory, 'dest');

        $filesystem->copy($directory, 'dest');
    }

    public function testContents()
    {
        $file = $this->tmpDir . '/' . uniqid();

        $this->filesystem->putContents($file, 'foo');
        $this->assertSame('foo', $this->filesystem->getContents($file));

        $this->filesystem->setContents($file, 'bar');
        $this->assertSame('bar', $this->filesystem->getContents($file));

        $this->filesystem->appendContents($file, 'baz');
        $this->assertSame('barbaz', $this->filesystem->getContents($file));

        $this->filesystem->prependContents($file, 'qux');
        $this->assertSame('quxbarbaz', $this->filesystem->getContents($file));
    }

    public function testListFilesAndListDirectories()
    {
        $directory = $this->tmpDir . '/' . uniqid();
        mkdir($directory);

        $subDirectory1 = $directory . '/sub1';
        mkdir($subDirectory1);

        $subDirectory2 = $directory . '/sub2';
        mkdir($subDirectory2);

        $file1 = $directory . '/f1';
        touch($file1);

        $file2 = $directory . '/f2';
        touch($file2);

        $this->assertSame([$file1, $file2], $this->filesystem->listFiles($directory));
        $this->assertSame([$subDirectory1, $subDirectory2], $this->filesystem->listDirectories($directory));
    }

    public function testPathInfo()
    {
        $file = '/foo/bar.txt';

        $this->assertSame('/foo', $this->filesystem->getDirName($file));
        $this->assertSame('bar', $this->filesystem->getName($file));
        $this->assertSame('bar.txt', $this->filesystem->getBaseName($file));
        $this->assertSame('txt', $this->filesystem->getExtension($file));
    }
}