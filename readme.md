# klinson/aws-s3-minio

## Description
Flexible and feature-complete minio client for PHP, depend on [aws/aws-sdk-php](https://github.com/aws/aws-sdk-php)

## Installation

```bash
composer require klinson/aws-s3-minio
```

## How to use Minio to control object

### Loading the library
```php
require 'Minio/Autoloader.php';

Minio\Autoloader::register();
```

### Connecting to Minio
```php
$ObjectClient = new ObjectClient();
// Get the access url, expire at 60 second
$url = $ObjectClient->getObjectUrl($save_path, time() + 60);
```

```php
// Parameters passed using a named array:
$minio_config = [
    'key' => 'minio-key',
    'secret' => 'minio-secret',
    'region' => '',
    'version' => 'latest',
    'endpoint' => 'http://127.0.0.1:9000',
    'bucket' => 'minio-bucket',
];
$ObjectClient = new ObjectClient($minio_config);
```


