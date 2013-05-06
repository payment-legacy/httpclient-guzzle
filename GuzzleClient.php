<?php

namespace Payment\HttpClient;

use Guzzle\Http\Client;

class GuzzleClient implements HttpClientInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if(is_null($this->client))
        {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param null $content
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     * @throws HttpException
     */
    public function request($method, $url, $content = null, array $headers = array(), array $options = array())
    {
        try
        {
            $originalRequest = $this->getClient()->createRequest($method, $url, $headers, $content);

            if($method == HttpClientInterface::METHOD_POST && !array_key_exists('Content-Type', $headers)) {
                $originalRequest->setHeader('Content-Type', 'application/x-www-form-urlencoded');
            }

            $originalResponse = $originalRequest->send();

            return new NullResponse(
                $originalResponse->getStatusCode(),
                $originalResponse->getContentType(),
                $originalResponse->getBody(true),
                $originalResponse->getHeaders()->toArray()
            );
        }
        catch(\Exception $e)
        {
            throw new HttpException($e);
        }
    }
}