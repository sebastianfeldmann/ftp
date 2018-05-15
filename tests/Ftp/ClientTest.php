<?php
namespace SebastianFeldmann\Ftp;

use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     * Tests Client::ls
     */
    public function testList()
    {
        $client = new Client('ftp://foo:bar@example.com');
        $list   = $client->ls();

        $this->assertTrue($list[0]->isFile());
        $this->assertEquals('foo.txt', $list[0]->getFilename());
        $this->assertEquals(100, $list[0]->getSize());

        $this->assertTrue($list[1]->isFile());
        $this->assertEquals('bar.txt', $list[1]->getFilename());

        $this->assertFalse($list[2]->isFile());
        $this->assertEquals('fiz', $list[2]->getFilename());
    }
}
