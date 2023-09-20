<?php

namespace JFuentesTgn\OcrSpace;

use GuzzleHttp\Client as HttpClient;

class OcrAPI
{
    private $key;
    private $url;
    private $requestAt;
    private $guzzleOptions = [];
    
    public function __construct($apiKey, $url = '')
    {
        $this->key = $apiKey;
        $this->url = $url;
    }

    public function getApiKey()
    {
        return $this->key;
    }

    public function setApiKey($apiKey)
    {
        $this->key = $apiKey;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setGuzzleOptions($options) {
        $this->guzzleOptions= $options;
    }

    public function getRequestAt()
    {
        return $this->requestAt;
    }
    
    public function parseImageFile($imgFile, $options = [])
    {
        return $this->parseImage('file', fopen($imgFile, 'r'), $options);
    }

    public function parseImageUrl($imgUrl, $options = [])
    {
        return $this->parseImage('url', $imgUrl, $options);
    }

    public function parseImageBinary($imgBinary, $mimeType, $options = [])
    {
        return $this->parseImageBase64(base64_encode($imgBinary), $mimeType, $options);
    }

    public function parseImageBase64($imgBase64, $mimeType, $options = [])
    {
        return $this->parseImage('base64Image', 'data:' . $mimeType . ';base64,' . $imgBase64, $options);
    }

    protected function parseImage($fldName, $fldValue, $options = [])
    {
        $client = new HttpClient($this->guzzleOptions);

        $lang = isset($options['lang']) ? $options['lang'] : 'eng';
        $headers = [ 'apikey' => $this->key ];
        $multipart = [
                [ 'name' => 'language', 'contents' => 'eng' ],
                [ 'name' => $fldName, 'contents' => $fldValue ]
            ];
       foreach ($options as $option => $value) {
            $multipart[] = [ 'name' => $option, 'contents' => $value ];
        }
        $url = $this->url == '' ? 'https://api.ocr.space/parse/image' : $this->url;
        try {
            $this->requestAt = time();
            $response = $client->request('POST', $url, ['headers' => $headers, 'multipart' => $multipart]);
        } catch (\Exception $e) {
            if (preg_match("/maximum ([0-9]{1,}) number of times within ([0-9]{1,} seconds)/", $e->getMessage(), $matches) ) {
                throw new OcrLimitException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw new OcrException('Error connecting to service URL: ' . $url, 0, $e);
            }
        }

        $code = $response->getStatusCode();
        if ($code != 200) {
            throw new OcrException('HTTP error returned from service URL: ' . $url, $code);
        }
        $body = $response->getBody();
        return new OcrResponse(json_decode($body));
    }
}
