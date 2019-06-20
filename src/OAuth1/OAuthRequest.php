<?php


namespace App\OAuth1;


use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * TODO: if you want to use it, please clean up this mess ;)
 */
class OAuthRequest
{
    /**
     * @param HttpClientInterface $httpClient
     * @param string $method
     * @param string $requestUri
     * @param array $queryParams
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function request(HttpClientInterface $httpClient, string $method, string $requestUri, array $queryParams = [])
    {
        return $httpClient->request($method, $requestUri, $this->options($method, $requestUri, $queryParams));
    }

    /**
     * Generates options array for the http-client's request() method: query params, headers, etc.
     *
     * @param string $method
     * @param string $requestUri
     * @param array $queryParams
     * @return array
     */
    private function options(string $method, string $requestUri, array $queryParams = [])
    {
        // its only proof of concept, so lets begin hard coding!
        $consumerKey = 'key'; // we put this into query params
        $consumerSecret = 'secret'; // we use this in signature generator

        $queryParams = array_merge($queryParams, [
            'oauth_consumer_key' => $consumerKey,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => date('U'),
            'oauth_nonce' => $this->noonce(),
            'oauth_version' => '1.0'
        ]);

        ksort($queryParams);
        $baseString = $this->baseString($method, $requestUri, $queryParams);

        $queryParams['oauth_signature'] = $this->sign($baseString, $consumerSecret . '&');

        return [
            'query' => $queryParams
        ];
    }

    /**
     * Prepare query for the signing process, pain in the ... ;)
     * Its not exactly url encode, so you have to manually translate special url chars (at least):
     * & becomes %26
     * space becomes %20
     * = becomes %3D
     *
     * @param string $method
     * @param string $requestUri
     * @param array $queryParams
     * @return string
     */
    private function baseString(string $method, string $requestUri, array $queryParams)
    {
        $params = http_build_query($queryParams, null, '%26', PHP_QUERY_RFC3986);
        return sprintf('%s&%s&%s', $method, urlencode($requestUri), strtr($params, ['=' => '%3D']));
    }

    /**
     * Signature generation
     *
     * @param string $baseString
     * @param string $key
     * @return string
     */
    private function sign(string $baseString, string $key)
    {
        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }

    /**
     * Used to generate noonce value
     * Must be unique within the corresponding timestamp
     *
     * @return string
     */
    private function noonce()
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
