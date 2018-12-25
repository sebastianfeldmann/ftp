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
 * Class Client
 *
 * @package SebastianFeldmann\Ftp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/ftp
 * @since   Class available since Release 0.9.0
 *
 * @method bool   cdUp()                                     - Changes to the parent directory
 * @method string chDir(string $directory)                   - Changes the current directory on a FTP server
 * @method string mdtm(string $file)                         - Returns last modification time from given file
 * @method bool   mkDir(string $path)                        - Create a directory on the FTP server
 * @method array  mlsd(string $path)                         - Return list of file info arrays (>= php 7.2.0)
 * @method array  nlist(string $path)                        - Returns list of files in given dir
 * @method bool   pasv(bool $passive)                        - Sets the ftp passive mode on or off
 * @method bool   put(string $name, string $file, int $mode) - Uploads a file to the FTP server
 * @method string pwd()                                      - Returns current working directory
 * @method int    size(string $file)                         - Returns given files sizes in bytes
 */
class Client
{
    /**
     * PHP FTP connection resource.
     *
     * @var resource
     */
    private $connection;

    /**
     * Host to connect to.
     *
     * @var string
     */
    private $host;

    /**
     * Port to connect to.
     *
     * @var int
     */
    private $port;

    /**
     * User to login.
     *
     * @var string
     */
    private $user;

    /**
     * Password to login.
     *
     * @var string
     */
    private $password;

    /**
     * Use passive ftp mode
     *
     * @var bool
     */
    private $passive;

    /**
     * Use ftps connection
     *
     * @var bool
     */
    private $isSecure;

    /**
     * Client constructor.
     *
     * @param string $url
     * @param bool   $passive
     * @param bool   $isSecure
     */
    public function __construct(string $url, bool $passive = false, bool $isSecure = false)
    {
        if (!extension_loaded('ftp')) {
            throw new RuntimeException('FTP extension is not loaded.');
        }
        $this->passive = $passive;
        $this->setup($url);
        $this->login();
    }

    /**
     * Determine if file is a directory.
     *
     * @param  string $name
     * @return bool
     */
    public function isDir(string $name)
    {
        $current = $this->pwd();
        try {
            $this->chDir($name);
            $this->chDir($current);
            return true;
        } catch (\Exception $e) {
            // do nothing
        }
        return false;
    }

    /**
     * Returns to the home directory.
     *
     * @return void
     */
    public function chHome()
    {
        $this->chDir('');
    }

    /**
     * Return list of all files in directory.
     *
     * @param  string $path
     * @return \SebastianFeldmann\Ftp\File[]
     * @throws \Exception
     */
    public function ls(string $path = '') : array
    {
        return version_compare(PHP_VERSION, '7.2.0', '>=')
            ? $this->ls72($path)
            : $this->lsLegacy($path);

    }

    /**
     * Return list of all files in directory for php 7.2.0 and higher.
     *
     * @param  string $path
     * @return \SebastianFeldmann\Ftp\File[]
     * @throws \Exception
     */
    private function ls72(string $path) : array
    {
        $list = [];
        foreach ($this->mlsd($path) as $fileInfo) {
            $list[] = new File($fileInfo);
        }
        return $list;
    }

    /**
     * Return list of all files in directory for php versione below 7.2.0.
     *
     * @param  string $path
     * @return \SebastianFeldmann\Ftp\File[]
     * @throws \Exception
     */
    private function lsLegacy(string $path) : array
    {
        $list = [];
        foreach ($this->nlist($path) as $name) {
            $type   = $this->isDir($name) ? 'dir' : 'file';
            $size   = $this->size($name);
            $mtime  = $this->mdtm($name);

            if ($mtime == -1) {
                throw new RuntimeException('FTP server doesnt support \'ftp_mdtm\'');
            }

            $list[] = new File(['name' => $name, 'modify' => $mtime, 'type' => $type, 'size' => $size]);
        }
        return $list;
    }

    /**
     * Return list of directories in given path.
     *
     * @param  string $path
     * @return array
     * @throws \Exception
     */
    public function lsDirs(string $path = '') : array
    {
        return array_filter(
            $this->ls($path),
            function(File $file) {
                return $file->isDir();
            }
        );
    }

    /**
     * Return list of files in given path.
     *
     * @param  string $path
     * @return array
     * @throws \Exception
     */
    public function lsFiles(string $path = '') : array
    {
        return array_filter(
            $this->ls($path),
            function(File $file) {
                return $file->isFile();
            }
        );
    }

    /**
     * Upload local file to ftp server.
     *
     * @param  string $file Path to local file that should be uploaded.
     * @param  string $path Path to store the file under.
     * @param  string $name Filename on the ftp server.
     * @return void
     */
    public function uploadFile(string $file, string $path, string $name)
    {
        // to store the file we have to make sure the directory exists
        foreach ($this->extractDirectories($path) as $dir) {
            // if change to directory fails
            // create the dir and change into it afterwards
            try {
                $this->chDir($dir);
            } catch (\Exception $e) {
                $this->mkDir($dir);
                $this->chDir($dir);
            }
        }
        if (!$this->put($name, $file, FTP_BINARY)) {
            $error   = error_get_last();
            $message = $error['message'];
            throw new RuntimeException(sprintf('error uploading file: %s - %s', $file, $message));
        }
    }

    /**
     * Setup local member variables by parsing the ftp url.
     *
     * @param string $url
     */
    private function setup(string $url)
    {
        $parts          = \parse_url($url);
        $this->host     = $parts['host'] ?? '';
        $this->port     = $parts['port'] ?? 21;
        $this->user     = $parts['user'] ?? '';
        $this->password = $parts['pass'] ?? '';
    }

    /**
     * Setup ftp connection
     *
     * @throws \RuntimeException
     */
    private function login()
    {
        if (empty($this->host)) {
            throw new RuntimeException('no host to connect to');
        }

        $old = error_reporting(0);

        $this->connection = ($this->isSecure ? ftp_ssl_connect($this->host, $this->port) : ftp_connect($this->host, $this->port));

        if (!$this->connection) {
            error_reporting($old);
            throw new RuntimeException(sprintf('unable to connect to ftp server %s', $this->host));
        }

        if (!ftp_login($this->connection, $this->user, $this->password)) {
            error_reporting($old);
            throw new RuntimeException(
                sprintf('authentication failed for %s@%s', $this->user, $this->host)
            );
        }
        // set passive mode if needed
        $this->pasv($this->passive);
        error_reporting($old);
    }

    /**
     * Return list of remote directories to travers.
     *
     * @param  string $path
     * @return array
     */
    private function extractDirectories(string $path) : array
    {
        $remoteDirs = [];
        if (!empty($path)) {
            $remoteDirs = explode('/', $path);
            // fix empty first array element for absolute path
            if (substr($path, 0, 1) === '/') {
                $remoteDirs[0] = '/';
            }
            $remoteDirs = array_filter($remoteDirs);
        }
        return $remoteDirs;
    }

    /**
     * Handle all ftp_* functions.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        $function = 'ftp_' . strtolower($name);
        if (!function_exists($function)) {
            throw new RuntimeException(sprintf('invalid method call: %s', $function));
        }
        $old = error_reporting(0);
        array_unshift($args, $this->connection);
        if (!$result = call_user_func_array($function, $args)) {
            $error = error_get_last();
            error_reporting($old);
            throw new RuntimeException($error['message']);
        }
        error_reporting($old);
        return $result;
    }
}
