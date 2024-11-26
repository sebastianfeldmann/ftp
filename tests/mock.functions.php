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

use RuntimeException;

/**
 * Mock internal ftp_connect.
 *
 * @param  string $host
 * @param  int    $port
 * @return array
 */
function ftp_connect($host, $port)
{
    return ['host' => $host, 'port' => $port];
}


/**
 * Mock internal ftp_ssl_connect.
 *
 * @param  string $host
 * @param  int    $port
 * @return array<string, mixed>
 */
function ftp_ssl_connect($host, $port)
{
    return ['host' => $host, 'port' => $port];
}

/**
 * Mock internal ftp_login.
 *
 * @param  mixed $connection
 * @param  string $user
 * @param  string $password
 * @return bool
 * @throws \RuntimeException
 */
function ftp_login($connection, $user, $password)
{
    if ($connection['host'] != 'example.com') {
        throw new RuntimeException('invalid connection');
    }
    if ($connection['port'] != 21) {
        throw new RuntimeException('invalid connection');
    }
    if (empty($user)) {
        throw new RuntimeException('invalid user');
    }
    if (empty($password)) {
        throw new RuntimeException('invalid password');
    }
    return true;
}

/**
 * Mock internal call_user_func_array.
 *
 * @param  string $name
 * @param  array  $args
 * @return bool
 */
function call_user_func_array($name = '', $args = '', $isTwice = false)
{
    static $iterations;

    if ($isTwice) {
        if ($name === 'ftp_mlsd' || $name === 'ftp_mdtm' || $name === 'ftp_nlist' || $name === 'ftp_chdir' || $name === 'ftp_pwd' || $name === 'ftp_size') {
            $iterations[$name] = 0;
        }

        return true;
    }

    $iteration = $iterations[$name] ?? 0;
    $iterations[$name]++;

    $return = [
        'ftp_nlist' => [
            ['foo.txt', 'bar.txt', 'fiz', 'baz'],
        ],
        'ftp_mlsd' => [
            [
                ['name' => 'foo.txt', 'modify' => '20180101123055', 'type' => 'file', 'size' => 100],
                ['name' => 'bar.txt', 'modify' => '20180101123056', 'type' => 'file', 'size' => 200],
                ['name' => 'fiz',     'modify' => '20180101123057', 'type' => 'dir',  'size' => 1],
                ['name' => 'baz',     'modify' => '20180101123058', 'type' => 'dir',  'size' => 1],
            ]
        ],
        'ftp_pwd'   => [
            '/root',
        ],
        'ftp_chdir' => [
            false,
            false,
            true,
            true,
            true,
            true,
        ],
        'ftp_mdtm' => [
            '20180101123055',
            '20180101123056',
            '20180101123057',
            '20180101123058'
        ],
        'ftp_size' => [
            100,
            200,
            1,
            1,
            1,
            1,
        ],
        'ftp_put' => [
            true,
        ],
    ];

    return $return[$name][$iteration] ?? true;
}
