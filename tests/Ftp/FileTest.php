<?php
namespace SebastianFeldmann\Ftp;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * Tests File::__construct
     *
     * @expectedException \Exception
     */
    public function testConstructNoType()
    {
        $file = new File([]);
    }

    /**
     * Tests File::__construct
     *
     * @expectedException \Exception
     */
    public function testConstructNoName()
    {
        $file = new File(['type' => 'file']);
    }

    /**
     * Tests File::__construct
     */
    public function testConstructNoMtime()
    {
        $file = new File(['type' => 'file', 'name' => 'foo.txt', 'size' => 100]);
        $date = $file->getLastModifyDate();

        $this->assertTrue(is_a($date, \DateTimeImmutable::class));
    }

    /**
     * Tests File::getUniqueName
     */
    public function testGetUnique()
    {
        $file = new File(['type' => 'file', 'name' => 'foo.txt', 'size' => 100, 'unique' => 'UN']);
        $this->assertEquals('UN', $file->getUniqueName());
    }

    /**
     * Tests File::isDir
     */
    public function testIsDir()
    {
        $file = new File(['type' => 'file', 'name' => 'foo.txt', 'size' => 100, 'unique' => 'UN']);
        $this->assertFalse($file->isDir());
        $this->assertTrue($file->isFile());
    }

    /**
     * Tests File::isDir
     */
    public function testIsFile()
    {
        $file = new File(['type' => 'dir', 'name' => 'foo.txt', 'size' => 100, 'unique' => 'UN']);
        $this->assertTrue($file->isDir());
        $this->assertFalse($file->isFile());
    }
}
