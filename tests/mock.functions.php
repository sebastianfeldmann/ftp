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

/**
 * Mock internal ftp_connect.
 *
 * @param  string $host
 * @param  string $port
 * @return array
 */
function ftp_connect($host, $port)
{
    return ['host' => $host, 'port' => $port];
}

/**
 * Mock internal ftp_login.
 *
 * @param  array  $connection
 * @param  string $user
 * @param  string $password
 * @return bool
 * @throws \Exception
 */
function ftp_login($connection, $user, $password)
{
    if ($connection['host'] != 'example.com') {
        throw new \Exception('invalid connection');
    }
    if ($connection['port'] != 21) {
        throw new \Exception('invalid connection');
    }
    if (empty($user)) {
        throw new \Exception('invalid user');
    }
    if (empty($password)) {
        throw new \Exception('invalid password');
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
function call_user_func_array($name, $args)
{
    static $iterations;

    $iteration = $iterations[$name] ?? 0;
    $iterations[$name]++;

    $return = [
        'ftp_nlist' => [
            ['foo.txt', 'bar.txt', 'fiz', 'baz'],
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
