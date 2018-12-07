<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-12-6
 * Time: 下午5:49
 */

namespace Minio\Object;

use Aws\Exception\AwsException;
use Minio\MinioClient;

class BucketClient extends MinioClient
{
    /**
     * 创建桶
     * @param $bucket
     * @author klinson <klinson@163.com>
     * @return bool|string 成功返回桶名，失败返回false
     */
    public function createBucket(String $bucket = null)
    {
        if (is_null($bucket)) {
            $bucket = $this->bucket;
        }
        try {
            $result = $this->S3Client->createBucket([
                'Bucket'     => $bucket,
            ]);
            return $bucket;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 删除桶
     * @param null|string $bucket
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function removeBucket(String $bucket = null)
    {
        if (is_null($bucket)) {
            $bucket = $this->bucket;
        }
        try {
            $result = $this->S3Client->deleteBucket([
                'Bucket'     => $bucket,
            ]);
            return true;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 获取列表桶
     * @author klinson <klinson@163.com>
     * @return array|bool 成功返回桶列表，失败返回false
     */
    public function listBuckets()
    {
        try {
            $result = $this->S3Client->listBuckets([]);
            $list = $result->get('Buckets');

            if (function_exists('format_bucket')) {
                $return = array_map('format_bucket', $list);
            } else {
                $return = array_map(function ($object) {
                    return [
                        'name' => $object['Name'],
                        'created_at' => $object['CreationDate']->getTimestamp(),
                    ];
                }, $list);
            }
            return $return;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 获取策略
     * @param string|null $bucket
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function getBucketPolicies(string $bucket = null)
    {
        if (is_null($bucket)) {
            $bucket = $this->bucket;
        }
        try {
            $result = $this->S3Client->getBucketPolicy([
                'Bucket'     => $bucket,
            ]);
            $policy = $result->get('Policy');
            $policyContent = json_decode($policy->getContents(), true);
            /*
array(2) {
["Version"]=>
  string(10) "2012-10-17"
["Statement"]=>
  array(5) {
                    [0]=>
    array(4) {
                        ["Effect"]=>
      string(5) "Allow"
                        ["Principal"]=>
      array(1) {
                            ["AWS"]=>
        array(1) {
                                [0]=>
          string(1) "*"
      }
      ["Action"]=>
      array(2) {
                            [0]=>
        string(20) "s3:GetBucketLocation"
                            [1]=>
        string(29) "s3:ListBucketMultipartUploads"
      }
      ["Resource"]=>
      array(1) {
                            [0]=>
        string(16) "arn:aws:s3:::kkk"
      }
    }
    [1]=>
    array(5) {
                        ["Effect"]=>
      string(5) "Allow"
                        ["Principal"]=>
      array(1) {
                            ["AWS"]=>
        array(1) {
                                [0]=>
          string(1) "*"
        }
      }
      ["Action"]=>
      array(1) {
                            [0]=>
        string(13) "s3:ListBucket"
      }
      ["Resource"]=>
      array(1) {
                            [0]=>
        string(16) "arn:aws:s3:::kkk"
      }
      ["Condition"]=>
      array(1) {
                            ["StringEquals"]=>
        array(1) {
                                ["s3:prefix"]=>
          array(2) {
                                    [0]=>
            string(10) "public_all"
                                    [1]=>
            string(11) "public_read"
          }
        }
      }
    }
    [2]=>
    array(4) {
                        ["Effect"]=>
      string(5) "Allow"
                        ["Principal"]=>
      array(1) {
                            ["AWS"]=>
        array(1) {
                                [0]=>
          string(1) "*"
        }
      }
      ["Action"]=>
      array(1) {
                            [0]=>
        string(12) "s3:GetObject"
      }
      ["Resource"]=>
      array(1) {
                            [0]=>
        string(29) "arn:aws:s3:::kkk/public_read*"
      }
    }
    [3]=>
    array(4) {
                        ["Effect"]=>
      string(5) "Allow"
                        ["Principal"]=>
      array(1) {
                            ["AWS"]=>
        array(1) {
                                [0]=>
          string(1) "*"
        }
      }
      ["Action"]=>
      array(4) {
                            [0]=>
        string(23) "s3:AbortMultipartUpload"
                            [1]=>
        string(15) "s3:DeleteObject"
                            [2]=>
        string(27) "s3:ListMultipartUploadParts"
                            [3]=>
        string(12) "s3:PutObject"
      }
      ["Resource"]=>
      array(1) {
                            [0]=>
        string(30) "arn:aws:s3:::kkk/public_write*"
      }
    }
    [4]=>
    array(4) {
                        ["Effect"]=>
      string(5) "Allow"
                        ["Principal"]=>
      array(1) {
                            ["AWS"]=>
        array(1) {
                                [0]=>
          string(1) "*"
        }
      }
      ["Action"]=>
      array(5) {
                            [0]=>
        string(27) "s3:ListMultipartUploadParts"
                            [1]=>
        string(12) "s3:PutObject"
                            [2]=>
        string(23) "s3:AbortMultipartUpload"
                            [3]=>
        string(15) "s3:DeleteObject"
                            [4]=>
        string(12) "s3:GetObject"
      }
      ["Resource"]=>
      array(1) {
                            [0]=>
        string(28) "arn:aws:s3:::kkk/public_all*"
      }
    }
  }
}
*/
            return $policyContent;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 设置策略，会覆盖原来设置的
     * @param array $policies
     * $policies = [
            'read' => ['read1', 'read2'], //只读
            'write' => ['write1', 'write2'],  //只写
            'read+write' => ['readwrite1', 'rw'] // 读+写
        ];
     * @param string|null $bucket
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function setBucketPolicies($policies = [], string $bucket = null)
    {
        if (is_null($bucket)) {
            $bucket = $this->bucket;
        }

        $policy_string = $this->getPolicyString($policies, $bucket);
        try {
            $result = $this->S3Client->putBucketPolicy([
                'Bucket'     => $bucket,
                'Policy'     => $policy_string
            ]);
            return true;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 删除所有策略
     * @param string|null $bucket
     * @author klinson <klinson@163.com>
     * @return bool
     */
    public function deleteBucketPolicies(string $bucket = null)
    {
        if (is_null($bucket)) {
            $bucket = $this->bucket;
        }
        try {
            $result = $this->S3Client->deleteBucketPolicy([
                'Bucket'     => $bucket,
            ]);
            return true;
        } catch (AwsException $awsException) {
            return $this->returnFalseWithSaveErrorInfo($awsException);
        }
    }

    /**
     * 生成policy设置字符串
     * @param $policies
     * @param $bucket
     * @author klinson <klinson@163.com>
     * @return string
     */
    protected function getPolicyString($policies, $bucket)
    {
        $policy_types = array_keys($policies);
        sort($policy_types);
        $policy_types = implode('&', $policy_types);
        switch ($policy_types) {
            case 'read':
                $paths = $policies['read'];
                $prefix_string = '"'.implode('","', $paths).'"';
                $resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $paths)
                    )
                    .'"';
                $str = <<<STR
{
	"Version": "2012-10-17",
	"Statement": [{
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetBucketLocation"],
		"Resource": ["arn:aws:s3:::$bucket"]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:ListBucket"],
		"Resource": ["arn:aws:s3:::$bucket"],
		"Condition": {
			"StringEquals": {
				"s3:prefix": [$prefix_string]
			}
		}
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetObject"],
		"Resource": [$resource]
	}]
}
STR;
                break;
            case 'write':
                $paths = $policies['write'];
                $resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $paths)
                    )
                    .'"';
                $str = <<<STR
{
	"Version": "2012-10-17",
	"Statement": [{
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetBucketLocation", "s3:ListBucketMultipartUploads"],
		"Resource": ["arn:aws:s3:::$bucket"]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:AbortMultipartUpload", "s3:DeleteObject", "s3:ListMultipartUploadParts", "s3:PutObject"],
		"Resource": [$resource]
	}]
}
STR;

                break;
            case 'read+write':
                $paths = $policies['read+write'];
                $prefix_string = '"'.implode('","', $paths).'"';
                $resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $paths)
                    )
                    .'"';
                $str = <<<STR
{
	"Version": "2012-10-17",
	"Statement": [{
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetBucketLocation", "s3:ListBucketMultipartUploads"],
		"Resource": ["arn:aws:s3:::$bucket"]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:ListBucket"],
		"Resource": ["arn:aws:s3:::$bucket"],
		"Condition": {
			"StringEquals": {
				"s3:prefix": [$prefix_string]
			}
		}
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetObject", "s3:ListMultipartUploadParts", "s3:PutObject", "s3:AbortMultipartUpload", "s3:DeleteObject"],
		"Resource": [$resource]
	}]
}
STR;
//                var_dump($str);exit;
                break;
            case 'read&read+write':
                $prefix_string = '"'.implode('","', array_merge($policies['read'], $policies['read+write'])).'"';
                $all_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['read+write'])
                    )
                    .'"';
                $read_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['read'])
                    )
                    .'"';
                $str = <<<STR
{
	"Version": "2012-10-17",
	"Statement": [{
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetBucketLocation", "s3:ListBucketMultipartUploads"],
		"Resource": ["arn:aws:s3:::$bucket"]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:ListBucket"],
		"Resource": ["arn:aws:s3:::$bucket"],
		"Condition": {
			"StringEquals": {
				"s3:prefix": [$prefix_string]
			}
		}
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:AbortMultipartUpload", "s3:DeleteObject", "s3:GetObject", "s3:ListMultipartUploadParts", "s3:PutObject"],
		"Resource": [$all_resource]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetObject"],
		"Resource": [$read_resource]
	}]
}
STR;

                break;
            case 'read+write&write':
                $prefix_string = '"'.implode('","', array_merge($policies['read'], $policies['read+write'])).'"';
                $all_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['read+write'])
                    )
                    .'"';
                $read_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['read'])
                    )
                    .'"';

                $str = <<<STR
{
	"Version": "2012-10-17",
	"Statement": [{
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetBucketLocation", "s3:ListBucket", "s3:ListBucketMultipartUploads"],
		"Resource": ["arn:aws:s3:::$bucket"]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:ListBucket"],
		"Resource": ["arn:aws:s3:::$bucket"],
		"Condition": {
			"StringEquals": {
				"s3:prefix": [$prefix_string]
			}
		}
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetObject"],
		"Resource": [$read_resource]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:AbortMultipartUpload", "s3:DeleteObject", "s3:ListMultipartUploadParts", "s3:PutObject"],
		"Resource": [$all_resource]
	}]
}
STR;

                break;
            case 'read&write':
                $prefix_string = '"'.implode('","', $policies['read']).'"';

                $read_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['read'])
                    )
                    .'"';
                $write_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['write'])
                    )
                    .'"';
                $str = <<<STR
{
	"Version": "2012-10-17",
	"Statement": [{
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:ListBucketMultipartUploads", "s3:GetBucketLocation", "s3:ListBucket"],
		"Resource": ["arn:aws:s3:::$bucket"]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:ListBucket"],
		"Resource": ["arn:aws:s3:::$bucket"],
		"Condition": {
			"StringEquals": {
				"s3:prefix": [$prefix_string]
			}
		}
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetObject"],
		"Resource": [$read_resource]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:PutObject", "s3:AbortMultipartUpload", "s3:DeleteObject", "s3:ListMultipartUploadParts"],
		"Resource": [$write_resource]
	}]
}
STR;

                break;
            case 'read&read+write&write':
                $prefix_string = '"'.implode('","', array_merge($policies['read'], $policies['read+write'])).'"';
                $all_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['read+write'])
                    )
                    .'"';
                $read_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['read'])
                    )
                    .'"';
                $write_resource = '"'
                    .implode(
                        '","',
                        array_map(function ($path) use ($bucket) {
                            return "arn:aws:s3:::$bucket/$path*";
                        }, $policies['write'])
                    )
                    .'"';
                $str = <<<STR
{
	"Version": "2012-10-17",
	"Statement": [{
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetBucketLocation", "s3:ListBucket", "s3:ListBucketMultipartUploads"],
		"Resource": ["arn:aws:s3:::$bucket"]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:ListBucket"],
		"Resource": ["arn:aws:s3:::$bucket"],
		"Condition": {
			"StringEquals": {
				"s3:prefix": [$prefix_string]
			}
		}
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:DeleteObject", "s3:GetObject", "s3:ListMultipartUploadParts", "s3:PutObject", "s3:AbortMultipartUpload"],
		"Resource": [$all_resource]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:GetObject"],
		"Resource": [$read_resource]
	}, {
		"Effect": "Allow",
		"Principal": {
			"AWS": ["*"]
		},
		"Action": ["s3:AbortMultipartUpload", "s3:DeleteObject", "s3:ListMultipartUploadParts", "s3:PutObject"],
		"Resource": [$write_resource]
	}]
}
STR;

                break;
            default:
                $str = '';
                break;
        }
        return $str;
    }
}