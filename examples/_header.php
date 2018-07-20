<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-7-18
 * Time: 上午10:06
 */

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../autoload.php';

// minio配置信息
$minio_config = [
    'key' => 'minio-key',
    'secret' => 'minio-secret',
    'region' => '',
    'version' => 'latest',
    'endpoint' => 'http://127.0.0.1:9000',
    'bucket' => 'minio-bucket',
];

// 打印错误信息
function echo_error_info(\Minio\Object\ObjectClient $ObjectClient)
{
    echo 'error_info: '.$ObjectClient->getErrorInfo() . PHP_EOL;
    echo 'error_code: '.$ObjectClient->getErrorCode() . PHP_EOL;
    echo 'error_message: '.$ObjectClient->getErrorMessage() . PHP_EOL;
}

// format object in function listObject()
function format_object($object) {
    return [
        'key' => $object['Key'],
        'size' => $object['Size'],
        'last_modified' => $object['LastModified']->getTimestamp(),
    ];
}