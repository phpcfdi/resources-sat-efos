<?php

 /*
 *  This file is part of the phpCfdi package.
 *  
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *  
 *  (c) 2020 phpCfdi
 *  
 */

declare( strict_types = 1 );

namespace PhpCfdi\Efos\Classes;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use PhpCfdi\Efos\Exceptions\DownloadException;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * The EfosHttpClient class is used to check if URL resource has changed and download.
 * 
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class EfosHttpClient
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    private $headers = null;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param string $url|null
     */
    public function __construct(LoggerInterface $logger, string $url = null)
    {
        $this->url = $url;
        $this->logger = $logger;
        $this->httpClient = HttpClient::create();
    }

    /**
     * Requests an HTTP resource.
     *
     * @param string $method
     * @param array $options
     * @return ResponseInterface|null
     */
    private function request(string $method, array $options = []) : ?ResponseInterface
    {
        try {
            $response = $this->httpClient->request($method, $this->url, $options);
            
            // The exception happens after the call to request(), at destruction time - ie outside of the try/catch.
            // Call any methods on $response (exception getInfo()) - that will make the client wait for the network, thus throw.
            $this->headers = $response->getHeaders();
        } catch (\Exception $th) {
            $this->logger->error($th->getMessage());
            return null;
        }
        return $response;
    }

    /**
     * Returns the Etag header
     *
     * @param string $previous_etag
     * @return string|null
     */
    public function getEtag() : ?string
    {
        $response = $this->request('HEAD');
        if ($response) {
            return $this->getHeader('etag');
        }
        return false;
    }

    /**
     * Returns a header value
     *
     * @param string $header
     * @return string|null
     */
    public function getHeader(string $header) :?string
    {
        return $this->headers[$header][0] ?? null;
    }

    /**
     * Download the resource 
     *
     * @throws DownloadException
     * @return string
     */
    public function download() : string
    {
        $response = $this->request('GET', [
            'buffer' => false,
        ]);
        if (200 !== $response->getStatusCode()) {
            throw new DownloadException('There was a problem trying to download the resource' . $this->url);
        }
        
        $file_path =  sys_get_temp_dir() . '/sat-efos-rosource';
        $file_handler = fopen($file_path, 'w');
        foreach ($this->httpClient->stream($response) as $chunk) {
            fwrite($file_handler, $chunk->getContent());
        }
        fclose($file_handler);

        return $file_path;
    }
}
