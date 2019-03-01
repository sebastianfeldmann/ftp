<?php
/**
 * This file is part of SebastianFeldmann\Ftp.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\Ftp;

use PHPUnit\Framework\TestCase;

/**
 * Class FileTest
 *
 * @package SebastianFeldmann\Ftp
 */
class FileTest extends TestCase
{
    /**
     * Tests File::__construct
     */
    public function testConstructNoType()
    {
        $this->expectedException(\Exception::class);
        $file = new File([]);
    }

    /**
     * Tests File::__construct
     */
    public function testConstructNoName()
    {
        $this->expectedException(\Exception::class);
        $file = new File(['type' => 'file']);
    }

    /**
     * Tests File::__construct
     */
    public function testConstructNoMtime()
    {
        $file = new File(['type' => 'file', 'name' => 'foo.txt', 'size' => 100]);
        $date = $file->getLastModifyDate();

        $this->assertInstanceOf(\DateTimeImmutable::class, $date);
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
     * Tests File::isFile
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
