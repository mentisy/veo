<?php
declare(strict_types=1);

namespace Avolle\Veo\Api;

use Avolle\Veo\Exception\VeoApiException;
use Cake\Http\Client;
use Cake\Http\Client\Response;
use Cake\Http\Cookie\Cookie;

/**
 * Veo API class
 *
 * Base class to build specific API calls onto.
 * Can also be used stand-alone.
 */
class VeoApi
{
    /**
     * API root URL
     */
    protected const HOST = 'app.veo.co/api';

    /**
     * Base path to append after constant HOST. Used when extending this class
     */
    protected const BASE_PATH = '';

    /**
     * Session cookie details
     */
    public const SESSION_COOKIE = [
        'name' => 'sessionid',
        'expiresAt' => null,
        'path' => '/',
        'domain' => 'app.veo.co',
    ];

    /**
     * API Client
     *
     * @var \Cake\Http\Client
     */
    public readonly Client $client;

    /**
     * Constructor method. Create an HTTP client and optionally authenticated with session id through a cookie
     *
     * @param string|null $sessionId Optional session id found through the web browser.
     */
    public function __construct(?string $sessionId = null)
    {
        $this->client = new Client([
            'basePath' => static::BASE_PATH,
            'host' => static::HOST,
            'scheme' => 'https',
            'redirect' => 1,
        ]);
        if (!empty($sessionId)) {
            $this->withAuthentication($sessionId);
        }
    }

    /**
     * Send authentication with client when performing requests.
     *
     * @param string $sessionId Session if to use in authentication cookie
     * @return void
     */
    protected function withAuthentication(string $sessionId): void
    {
        $cookie = new Cookie(
            self::SESSION_COOKIE['name'],
            $sessionId,
            null,
            self::SESSION_COOKIE['path'],
            self::SESSION_COOKIE['domain'],
        );
        $this->client->addCookie($cookie);
    }

    /**
     * Convert the JSON response from request into an array.
     * The method will complete safeguards to make sure the response is valid before returning
     *
     * @param \Cake\Http\Client\Response $response Response from request.
     * @return array
     * @throws \Avolle\Veo\Exception\VeoApiException if the response is invalid
     */
    protected function convertResponse(Response $response): array
    {
        if (!$response->isOk()) {
            throw new VeoApiException('Veo API returned status code ' . $response->getStatusCode());
        }
        $content = $response->getStringBody();
        if (empty($content)) {
            throw new VeoApiException('Veo API returned empty response.');
        }

        $data = json_decode($content, true);
        if (is_null($data)) {
            throw new VeoApiException('JSON decode failed from response.');
        }

        return $data;
    }
}
