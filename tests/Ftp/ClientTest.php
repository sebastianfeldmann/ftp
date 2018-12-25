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
 * Class ClientTest
 *
 * @package SebastianFeldmann\Ftp
 */
class ClientTest extends TestCase
{
    /**
     * Tests Client::ls
     */
    public function testList()
    {
        $client = new Client('ftp://foo:bar@example.com', false);
        $list   = $client->ls();

        $this->assertTrue($list[0]->isFile());
        $this->assertEquals('foo.txt', $list[0]->getFilename());
        $this->assertEquals(100, $list[0]->getSize());

        $this->assertTrue($list[1]->isFile());
        $this->assertEquals('bar.txt', $list[1]->getFilename());

        $this->assertFalse($list[2]->isFile());
        $this->assertEquals('fiz', $list[2]->getFilename());

        if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
            call_user_func_array('ftp_mlsd', null, true);
        } else {
            call_user_func_array('ftp_nlist', null, true);
            call_user_func_array('ftp_mdtm', null, true);
            call_user_func_array('ftp_pwd', null, true);
            call_user_func_array('ftp_chdir', null, true);
            call_user_func_array('ftp_size', null, true);
        }

        $client = new Client('ftps://foo:bar@example.com', true);
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
