<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-12-6
 * Time: 下午5:52
 */

require __DIR__.'/_header.php';

use Minio\Object\BucketClient;

$BucketClient = new BucketClient($minio_config);

$bucket = 'create-test';
// 创建桶
$res = $BucketClient->createBucket($bucket);
if ($res === false) {
    echo_error_info($BucketClient);
    exit;
}
var_dump($res);

// 获取列表
$list = $BucketClient->listBuckets();
var_dump($list);

// 设置策略
$policies = [
    'read' => ['read1', 'read2'],
    'write' => ['write1', 'write2'],
    'read+write' => ['readwrite1', 'rw']
];
$res = $BucketClient->setBucketPolicies($policies, $bucket);
if ($res === false) {
    echo_error_info($BucketClient);
    exit;
}
var_dump($res);

// 获取策略
$policies = $BucketClient->getBucketPolicies($bucket);
if ($policies === false) {
    echo_error_info($BucketClient);
    exit;
}
var_dump($policies);

// 删除策略
$res = $BucketClient->deleteBucketPolicies($bucket);
if ($res === false) {
    echo_error_info($BucketClient);
    exit;
}
var_dump($res);

//删除桶
$res = $BucketClient->removeBucket($bucket);
if ($res === false) {
    echo_error_info($BucketClient);
    exit;
}
var_dump($res);
