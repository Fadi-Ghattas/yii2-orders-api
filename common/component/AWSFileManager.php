<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 10/15/2016
 * Time: 11:33 AM
 */

namespace common\component;


use Aws\Common\Aws;
use Aws\S3\Model\ClearBucket;
use Aws\S3\Exception\S3Exception;
use common\helpers\Helpers;
use yii\helpers\Json;

class AWSFileManager extends Aws
{
    public $client;
    public $bucket;

    public function __construct($client, $bucket = '')
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }

    public function uploadedImageBase64ToBucket($bucket, $imageName, $imageBase64, $extension)
    {
        try {
            $result = $this->createObject([
                'Bucket' => $bucket,
                'Key' => $imageName . '.' . $extension,
                'Body' => base64_decode($imageBase64),
                'ContentType' => Helpers::getImageFileContentType($extension),
                'ACL' => 'public-read',
            ]);
            return ['success' => true, 'result' => Json::encode($result)];
        } catch (S3Exception $e) {
            return ['success' => false, 'result' => $e->getMessage()];
        }
        return ['success' => false, 'result' => 'not-implemented'];
    }

    public function uploadedMultipleImagesBase64ToBucket($bucket, $imageName, $imageBase64, $extension, $sizes)
    {
        $commands = array();
        foreach ($sizes as $size) {
            if ($size == 'Normal' || $size == 'normal') {
                $commands[] = $this->client->getCommand('PutObject', array(
                    'Bucket' => $bucket,
                    'Key' => "$imageName.$extension",
                    'Body' => base64_decode($imageBase64),
                    'ContentType' => Helpers::getImageFileContentType($extension),
                    'ACL' => 'public-read',
                ));
            } else {
                $commands[] = $this->client->getCommand('PutObject', array(
                    'Bucket' => $bucket.'/thumbnail',
                    'Key' => "$imageName.$extension", //$imageName /*. '_' . $size['suffix'] */. '.' . $extension,
                    'Body' => base64_decode(Helpers::resizeImage($imageBase64, $size['width'], $size['height'])),
                    'ContentType' => Helpers::getImageFileContentType($extension),
                    'ACL' => 'public-read',
                ));
            }
        }
        try {
            // Execute an array of command objects to do them in parallel
            $this->client->execute($commands);

            $result = array();
            // Loop over the commands, which have now all been executed
            foreach ($commands as $command) {
                $result [] = $command->getResult();
                // Do something with result
            }
            return ['success' => true, 'result' => Json::encode($result)];
        } catch (S3Exception $e) {
            return ['success' => false, 'result' => $e->getMessage()];
        }
        return ['success' => false, 'result' => 'not-implemented'];
    }

    public function createBucket()
    {
        try {

            $result = $this->client->createBucket(array('Bucket' => $this->bucket,'LocationConstraint'=>'ap-southeast-1'));
            $this->client->waitUntil('bucket_exists', array('Bucket' => $this->bucket));
            $clear = new ClearBucket($this->client, $this->bucket);
            $clear->clear();
            return $result;
        } catch (S3Exception $e) {
            return $e->getMessage();
        }
        return false;
    }

    public function deleteBucket()
    {
        try {
            $result = $this->client->deleteBucket(array('Bucket' => $this->bucket));
            $this->client->waitUntil('bucket_not_exists', array('Bucket' => $this->bucket));
            return $result;
        } catch (S3Exception $e) {
            return false;
        }
        return false;
    }

    public function createObject($args)
    {
        try {
            $result = $this->client->putObject($args);
            return $result;
        } catch (S3Exception $e) {
            return $e->getMessage();
        }
        return false;
    }

    public function deleteObject($args)
    {
        try {
            $result = $this->client->deleteObject($args);
            return $result;
        } catch (S3Exception $e) {
            return $e->getMessage();
        }
        return false;
    }

    public static function getResourcePrefix()
    {
        if (!isset($_SERVER['PREFIX']) || $_SERVER['PREFIX'] == 'hostname') {
            $_SERVER['PREFIX'] = crc32(gethostname()) . rand(0, 10000);
        }

        return $_SERVER['PREFIX'];
    }

}