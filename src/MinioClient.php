<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-7-18
 * Time: 上午9:33
 */

namespace Minio;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;

class MinioClient
{
    protected $S3Client;
    protected $bucket;
    protected $urlExpireTime = 24 * 60 * 60; //url默认一天有限时间

    protected $errorCode;
    protected $errorMessage;
    protected $errorInfo;

    public function __construct($config = [])
    {
        $this->S3Client = new S3Client([
            'credentials' => [
                'key'    => $config['key'] ?? '',
                'secret' => $config['secret'] ?? '',
            ],
            'region' => $config['region'] ?? '',
            'version' => $config['version'] ?? '',
            'endpoint' => $config['endpoint'] ?? '',
            'use_path_style_endpoint' => true, //minio必须开启
        ]);

        $this->bucket = $config['bucket'] ?? '';
    }

    public function getS3Client()
    {
        return $this->S3Client;
    }

    public function setS3Client(S3Client $S3Client)
    {
        $this->S3Client = $S3Client;
        return $this;
    }

    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
        return $this;
    }

    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * 返回bucket未设置的错误信息
     * @author klinson <klinson@163.com>
     * @return bool false
     */
    protected function returnBucketNoSetError()
    {
        $this->errorCode = '400';
        $this->errorMessage = 'Bucket No Set';
        $this->errorInfo = $this->errorMessage . " [{$this->errorCode}] ";
        return false;
    }

    /**
     * 返回false并保存错误信息
     * @param \Aws\Exception\AwsException $awsException
     * @author klinson <klinson@163.com>
     * @return bool false
     */
    protected function returnFalseWithSaveErrorInfo(AwsException $awsException)
    {
        $this->errorCode = $awsException->getResponse()->getStatusCode();
        $this->errorMessage = $awsException->getResponse()->getReasonPhrase();
        $this->errorInfo = $awsException->getMessage();
        return false;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getErrorInfo()
    {
        return $this->errorInfo;
    }
}