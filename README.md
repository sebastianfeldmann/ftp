# FTP 
A tiny PHP FTP wrapper.

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
