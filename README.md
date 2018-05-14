# FTP 
A tiny PHP FTP wrapper.

[![Latest Stable Version](https://poser.pugx.org/sebastianfeldmann/ftp/v/stable.svg)](https://packagist.org/packages/sebastianfeldmann/ftp)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/sebastianfeldmann/ftp.svg?v1)](https://packagist.org/packages/sebastianfeldmann/ftp)
[![License](https://poser.pugx.org/sebastianfeldmann/ftp/license.svg)](https://packagist.org/packages/sebastianfeldmann/ftp)
[![Build Status](https://travis-ci.org/sebastianfeldmann/ftp.svg?branch=master)](https://travis-ci.org/sebastianfeldmann/ftp)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sebastianfeldmann/ftp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sebastianfeldmann/ftp/?branch=master)

## List all files
```php
$ftp = new SebastianFeldmann\Ftp\Client('ftp://user:password@example.com');
foreach ($ftp->ls() as $file) {
    echo $item->getFilename() . PHP_EOL;
}
```

## List only directories
```php
$ftp = new SebastianFeldmann\Ftp\Client('ftp://user:password@example.com');
foreach ($ftp->lsDirs() as $file) {
    echo $item->getFilename() . PHP_EOL;
}
```

## List without directories
```php
$ftp = new SebastianFeldmann\Ftp\Client('ftp://user:password@example.com');
foreach ($ftp->lsFiles() as $file) {
    echo $item->getFilename() . PHP_EOL;
}
```

## Upload a file
```php
$ftp = new SebastianFeldmann\Ftp\Client('ftp://user:password@example.com');
$ftp->uploadFile($pathToLocalFile, 'foo/bar/baz', 'filname.zip');
```
