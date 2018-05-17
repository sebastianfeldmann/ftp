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

use DateTimeImmutable;
use RuntimeException;

/**
 * Class File
 *
 * @package SebastianFeldmann\Ftp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/ftp
 * @since   Class available since Release 0.9.0
 */
class File
{
    /**
     * Unique file id.
     *
     * @var string
     */
    private $unique;

    /**
     * Last modification date.
     *
     * @var \DateTimeImmutable
     */
    private $modify;

    /**
     * Filename.
     *
     * @var string
     */
    private $name;

    /**
     * Size in bytes.
     *
     * @var int
     */
    private $size;

    /**
     * File type 'dir' or 'file'.
     *
     * @var string
     */
    private $type;

    /**
     * File constructor.
     *
     * @param  array $ftpInfo
     * @throws \Exception
     */
    public function __construct(array $ftpInfo)
    {
        if (!isset($ftpInfo['type'])) {
            throw new RuntimeException('invalid file info, index type is missing');
        }
        if (!isset($ftpInfo['name'])) {
            throw new RuntimeException('invalid file info, index name is missing');
        }
        $this->type   = $ftpInfo['type'];
        $this->name   = $ftpInfo['name'];
        $this->unique = $ftpInfo['unique'] ?? '';
        $this->size   = $ftpInfo['size']   ?? 0;
        $this->setModificationDate($ftpInfo);
    }

    /**
     * Set modification date.
     *
     * @param  array $ftpInfo
     * @throws \Exception
     */
    private function setModificationDate(array $ftpInfo)
    {
        $modDate      = !empty($ftpInfo['modify'])
                      ? DateTimeImmutable::createFromFormat('YmdHis', $ftpInfo['modify'])
                      : false;
        $this->modify = !empty($modDate) ? $modDate : new DateTimeImmutable();
    }

    /**
     * Return unique identifier.
     *
     * @return string
     */
    public function getUniqueName() : string
    {
        return $this->unique;
    }

    /**
     * Return last modification date.
     *
     * @return \DateTimeImmutable
     */
    public function getLastModifyDate() : DateTimeImmutable
    {
        return $this->modify;
    }

    /**
     * Return the file name.
     *
     * @return string
     */
    public function getFilename() : string
    {
        return $this->name;
    }

    /**
     * Return file size in bytes.
     *
     * @return int
     */
    public function getSize() : int
    {
        return $this->size;
    }

    /**
     * Return true if a directory.
     *
     * @return bool
     */
    public function isDir() : bool
    {
        return $this->type === 'dir';
    }

    /**
     * Return true if not a directory.
     *
     * @return bool
     */
    public function isFile() : bool
    {
        return $this->type === 'file';
    }
}
