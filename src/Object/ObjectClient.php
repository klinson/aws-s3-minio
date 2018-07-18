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