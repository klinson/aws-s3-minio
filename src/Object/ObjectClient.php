<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-7-18
 * Time: 上午9:50
 */
namespace Minio\Object;
use Aws\Exception\AwsException;
use Minio\MinioClient;

class ObjectClient extends MinioClient
{
    /**
     * （通过文件路径）上传对象
     * @param string $localObjectPath 本地对象路径，支持相对和绝对路径
     * @param string|null $storageSavePath minio存储路径，自动创建文件夹，如test-dir/test-file.txt, 注意开头不能是 “/”, 回报错
     * @author klinson <klinson@163.com>
     * @return string|bool 成功返回真实的$storageSavePath
     */
    public function putObjectBySavePath($localObjectPath, $storageSavePath = null)
    {
        return $this->putObjectBySavePathToBucket($this->bucket, $localObjectPath, $storageSavePath);
    }

    /**
     * （通过文件路径）上传对象（指定bucket）
     * @param string $bucket
     * @param string $localObjectPath 本地对象路径，支持相对和绝对路径
     * @param string|null $storageSavePath minio存储路径，自动创建文件夹，如test-dir/test-file.txt, 注意开头不能是 “/”, 回报错
     * @author klinson <klinson@163.com>
     * @return string|bool 成功返回真实的$storageSavePath
     */
    public function putObjectBySavePathToBucket(string $bucket, string $localObjectPath, string $storageSavePath = null)
    {
        try {
            if (empty($bucket)) {
                return $this->returnBucketNoSetError();
            }

            if ($storageSavePath === null) {
                $storageSavePath = $localObjectPath;
            }
            $storageSavePath = $this->formatStorageSavePath($storageSavePath);

            // 下载文件的内容
            $result = $this->S3Client->putObject([
                'Bucket'     => $bucket,
                'Key'        => $storageSavePath,
                'SourceFile' => $localObjectPath,
            ]);

            return $storageSavePath;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 写入内容到对象并上传
     * @param string $storageSavePath
     * @param string $content
     * @author klinson <klinson@163.com>
     * @return string|bool 成功返回真实的$storageSavePath
     */
    public function putObjectByContent(string $storageSavePath, string $content)
    {
        return $this->putObjectByContentToBucket($this->bucket, $storageSavePath, $content);
    }

    /**
     * 写入内容到对象并上传（指定bucket）
     * @param string $bucket
     * @param string $storageSavePath
     * @param string $content
     * @author klinson <klinson@163.com>
     * @return string|bool 成功返回真实的$storageSavePath
     */
    public function putObjectByContentToBucket(string $bucket, string $storageSavePath, string $content)
    {
        try {
            if (empty($bucket)) {
                return $this->returnBucketNoSetError();
            }

            $storageSavePath = $this->formatStorageSavePath($storageSavePath);

            $result = $this->S3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $storageSavePath,
                'Body'   => $content
            ]);

            return $storageSavePath;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 获取文件并另存到本地
     * @param string $storageSavePath
     * @param string $localSaveAsPath
     * @author klinson <klinson@163.com>
     * @return bool|mixed|\GuzzleHttp\Psr7\LazyOpenStream
     */
    public function getObjectSaveAs(string $storageSavePath, string $localSaveAsPath)
    {
        return $this->getObjectInBucketSaveAs($this->bucket, $storageSavePath, $localSaveAsPath);
    }

    /**
     * 获取文件并另存到本地（指定bucket）
     * @param save_path: object_example/putObjectByContent.txt
aram string $bucket
     * @param string $storageSavePath
     * @param string $localSaveAsPath
     * @author klinson <klinson@163.com>
     * @return bool|mixed|\GuzzleHttp\Psr7\LazyOpenStream
     */
    public function getObjectInBucketSaveAs(string $bucket, string $storageSavePath, string $localSaveAsPath)
    {
        return $this->getObjectInBucket($bucket, $storageSavePath, $localSaveAsPath);
    }

    /**
     * 获取文件
     * @param string $storageSavePath
     * @author klinson <klinson@163.com>
     * @return bool|mixed|\GuzzleHttp\Psr7\LazyOpenStream
     */
    public function getObject(string $storageSavePath)
    {
        return $this->getObjectInBucket($this->bucket, $storageSavePath);
    }

    /**
     * 获取文件（指定bucket）
     * @param string $bucket
     * @param string $storageSavePath
     * @param string $localSaveAsPath
     * @author klinson <klinson@163.com>
     * @return bool|mixed|\GuzzleHttp\Psr7\LazyOpenStream
     */
    public function getObjectInBucket(string $bucket, string $storageSavePath, string $localSaveAsPath = null)
    {
        try {
            if (empty($bucket)) {
                return $this->returnBucketNoSetError();
            }

            $param = [
                'Bucket' => $bucket,
                'Key'    => $storageSavePath,
            ];
            if (! is_null($localSaveAsPath)) {
                $param = [
                    'Bucket' => $bucket,
                    'Key'    => $storageSavePath,
                    'SaveAs' => $localSaveAsPath
                ];
            }
            // 下载文件的内容
            $result = $this->S3Client->getObject($param);

            return $result['Body'];
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 获取对象（预览/下载）URL（指定bucket）
     * @param $storageSavePath
     * @param null $expiredAt
     * @author klinson <klinson@163.com>
     * @return string
     */
    public function getObjectUrl(string $storageSavePath, $expiredAt = null)
    {
        return $this->getObjectUrlInBucket($this->bucket, $storageSavePath, $expiredAt);
    }

    /**
     * 获取对象（预览/下载）URL
     * @param string $bucket
     * @param string $storageSavePath
     * @param null|int|string|\DateTime $expiredAt The time at which the URL should
     *     expire. This can be a Unix timestamp, a PHP DateTime object, or a
     *     string that can be evaluated by strtotime.
     * @author klinson <klinson@163.com>
     * @return string
     */
    public function getObjectUrlInBucket(string $bucket, string $storageSavePath, $expiredAt = null)
    {
        // Get a command object from the client
        $command = $this->S3Client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $storageSavePath
        ]);

        if (is_null($expiredAt)) {
            $expiredAt = time() + $this->urlExpireTime;
        }
        // Create a pre-signed URL for a request with duration
        $presignedRequest = $this->S3Client->createPresignedRequest($command, $expiredAt);

        $presignedUrl =  (string)  $presignedRequest->getUri();
        return $presignedUrl;
    }

    /**
     * 删除对象 (可批量)（指定bucket）
     * @param string $bucket
     * @param array|string $storageSavePath
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function removeObjectInBucket(string $bucket, $storageSavePath)
    {
        try {
            if (is_array($storageSavePath)) {
                $this->S3Client->deleteObjects([
                    'Bucket'  => $bucket,
                    'Delete' => [
                        'Objects' => array_map(function ($key) {
                            return ['Key' => $key];
                        }, $storageSavePath)
                    ],
                ]);
            } else {
                $this->S3Client->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $storageSavePath
                ]);
            }
            return true;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 删除对象(可批量)
     * @param array|string $storageSavePath
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function removeObject($storageSavePath)
    {
        return $this->removeObjectInBucket($this->bucket, $storageSavePath);
    }

    /*
     * 低级别的 listObjects() 方法将映射到底层 Amazon S3 REST API。每个 listObjects() 请求均返回最多有 1000 个对象的页面。如果您
     * 的存储桶中有超过 1000 个对象，则将截断您的响应，并且您需要发送其他 listObjects() 请求，以检索下一组 1000 个对象。
     *
     * 高级别 ListObjects 分页工具使列出存储桶中包含的对象的任务变得更轻松。要使用 ListObjects 分页工具创建对象列表，请执行从
     * Aws/AwsClientInterface 类继承的 Amazon S3 客户端 getPaginator() 方法，将 ListObjects 作为第一个参数，将包含从指定存储桶返
     * 回的对象的数组作为第二个参数。当作为 ListObjects 分页工具使用时，getPaginator() 方法将返回指定存储桶中包含的所有对象。不存在
     * 1000 个对象的限制，因此，您无需担心响应是否被截断。
     */
    /**
     * 获取1000个对象
     * @param string $prefix 前缀过滤
     * @author klinson <klinson@163.com>
     * @return array|bool
     */
    public function listObjects($prefix = '')
    {
        return $this->listObjectsInBucket($this->bucket, $prefix);
    }

    /**
     * 获取1000个对象（指定bucket）
     * @param string $bucket
     * @param string $prefix 前缀过滤
     * @author klinson <klinson@163.com>
     * @return array|bool
     */
    public function listObjectsInBucket(string $bucket, $prefix = '')
    {
        // Use the plain API (returns ONLY up to 1000 of your objects).
        try {
            $result = $this->S3Client->listObjects([
                'Bucket' => $bucket,
                'Prefix' => $prefix
            ]);

            if (empty($result['Contents'])) {
                return [];
            }
            if (function_exists('format_object')) {
                $return = array_map('format_object', $result['Contents']);
            } else {
                $return = array_map(function ($object) {
                    return [
                        'key' => $object['Key'],
                        'size' => $object['Size'],
                        'last_modified' => $object['LastModified']->getTimestamp(),
                    ];
                }, $result['Contents']);
            }

            return $return;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 获取所有对象
     * @param string $prefix 前缀过滤
     * @author klinson <klinson@163.com>
     * @return array|bool
     */
    public function getAllObjects($prefix = '')
    {
        return $this->getAllObjectsInBucket($this->bucket, $prefix = '');
    }

    /**
     * /**
     * 获取所有对象（指定bucket）
     * @param string $bucket
     * @param string $prefix 前缀过滤
     * @author klinson <klinson@163.com>
     * @return array|bool
     */
    public function getAllObjectsInBucket(string $bucket, $prefix = '')
    {
        try {

            $result = $this->S3Client->getPaginator('ListObjects', [
                'Bucket' => $bucket,
                'Prefix' => $prefix

            ]);

            if (empty($result->current()['Contents'])) {
                return [];
            }
            if (function_exists('format_object')) {
                $return = array_map('format_object', $result->current()['Contents']);
            } else {
                $return = array_map(function ($object) {
                    return [
                        'key' => $object['Key'],
                        'size' => $object['Size'],
                        'last_modified' => $object['LastModified']->getTimestamp(),
                    ];
                }, $result->current()['Contents']);
            }

            return $return;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 复制对象
     * @param string $sourceStorageSavePath 源对象路径
     * @param string|null $targetStorageSavePath 目标保存对象路径，默认是源对象路径加上部分前缀[copy_time()_]
     * @param string|null $targetBucket 目标对象bucket, 默认是源对象bucket
     * @author klinson <klinson@163.com>
     * @return bool|string $targetStorageSavePath
     */
    public function copyObject(string $sourceStorageSavePath, string $targetStorageSavePath = null, string $targetBucket = null)
    {
        return $this->copyObjectInBucket($sourceStorageSavePath, $this->bucket, $targetStorageSavePath, $targetBucket);
    }

    /**
     * 复制对象 （指定bucket）
     * @param string $sourceStorageSavePath 源对象路径
     * @param string $sourceBucket 源对象bucket
     * @param string|null $targetStorageSavePath 目标保存对象路径，默认是源对象路径加上部分前缀[copy_time()_]
     * @param string|null $targetBucket 目标对象bucket, 默认是源对象bucket
     * @author klinson <klinson@163.com>
     * @return bool|string $targetStorageSavePath
     */
    public function copyObjectInBucket(string $sourceStorageSavePath, string $sourceBucket, string $targetStorageSavePath = null, string $targetBucket = null)
    {
        try {
            if (is_null($targetBucket)) {
                $targetBucket = $sourceBucket;
            }
            if (is_null($targetStorageSavePath)) {
                $targetStorageSavePath = 'copy_' . time() . '_' . $sourceStorageSavePath;
            }

            $result = $this->S3Client->copyObject([
                'Bucket'     => $targetBucket,
                'Key'        => $targetStorageSavePath,
                'CopySource' => "{$sourceBucket}/{$sourceStorageSavePath}",
            ]);
            return $targetStorageSavePath;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 格式化处理, 去除前后的“/”
     * @param string $storageSavePath
     * @author klinson <klinson@163.com>
     * @return string
     */
    private function formatStorageSavePath(string $storageSavePath)
    {
        return trim($storageSavePath, '/');
    }

}