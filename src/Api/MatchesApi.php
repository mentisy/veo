<?php
declare(strict_types=1);

namespace Avolle\Veo\Api;

use Avolle\Veo\Entity\Maatch;

/**
 * Matches API class
 *
 * Request related to matches.
 */
class MatchesApi extends VeoApi
{
    /**
     * Base path for matches API requests
     */
    protected const BASE_PATH = 'app/matches';

    /**
     * URL params key that defines fields to load
     */
    protected const FIELDS_PARAM = 'fields';

    /**
     * URL params key that defined page to load
     */
    protected const PAGE_PARAM = 'page';

    /**
     * Essential fields to load
     */
    public const ESSENTIAL_FIELDS = [
        'camera',
        'created',
        'identifier',
        'processing_status',
        'slug',
        'title',
        'team',
        'team_name',
        'thumbnail',
        'url',
    ];

    /**
     * All the possible fields to load.
     *
     * @var array<string>
     */
    public array $allFields = [
        'camera',
        'created',
        'duration',
        'identifier',
        'is_paid_for_upload_minutes',
        'is_shared_recording',
        'permissions',
        'processing_status',
        'slug',
        'start',
        'title',
        'team',
        'team__name',
        'thumbnail',
        'url',
        'privacy',
    ];

    /**
     * Which fields to use in request
     *
     * @var array<string>
     */
    public array $useFields = [];

    /**
     * Get all matches for the specified page.
     *
     * @param int $page Page to load
     * @return array<\Avolle\Veo\Entity\Maatch>
     * @throws \Avolle\Veo\Exception\VeoApiException
     */
    public function matches(int $page = 1): array
    {
        $queryString = $this->buildQueryString($page);
        $response = $this->client->get('', $queryString);
        $matches = $this->convertResponse($response);

        return array_map([$this, 'createMatch'], $matches);
    }

    /**
     * Get a single match with the specified slug
     *
     * @param string $slug Match slug
     * @return \Avolle\Veo\Entity\Maatch
     * @throws \Avolle\Veo\Exception\VeoApiException
     */
    public function match(string $slug): Maatch
    {
        $response = $this->client->get($slug);
        $match = $this->convertResponse($response);

        return $this->createMatch($match);
    }

    /**
     * Get all matches for the specified club and team (if provided)
     *
     * @param string $club Club slug to get matches for
     * @param string|null $team Team slug (optional) to get matches for. If not provided, will get all matches for club
     * @return array<\Avolle\Veo\Entity\Maatch>
     * @throws \Avolle\Veo\Exception\VeoApiException
     */
    public function clubTeamMatches(string $club, ?string $team = null): array
    {
        $queryString = $this->buildQueryString();
        $queryString .= sprintf('&club=%s', $club);
        if (!empty($team)) {
            $queryString .= sprintf('&team=%s', $team);
        }

        $response = $this->client->get('', $queryString);
        $matches = $this->convertResponse($response);

        return array_map([$this, 'createMatch'], $matches);
    }

    /**
     * With this method you will only request what is deemed 'essential' fields.
     *
     * @return void
     */
    public function useEssentialFields(): void
    {
        $this->useFields = static::ESSENTIAL_FIELDS;
    }

    /**
     * Build a query string with the required fields and optionally a specified page
     *
     * @param int|null $page Optionally, append page to request URL params
     * @return string
     */
    protected function buildQueryString(?int $page = null): string
    {
        $query = [];
        $fields = !empty($this->useFields) ? $this->useFields : $this->allFields;
        foreach ($fields as $field) {
            $query[] = self::FIELDS_PARAM . '=' . urlencode($field);
        }
        if (isset($page)) {
            $query[] = self::PAGE_PARAM . '=' . urlencode((string)$page);
        }

        return implode('&', $query);
    }

    /**
     * Create a match entity with the given properties
     *
     * @param array $properties Match properties from array.
     * @return \Avolle\Veo\Entity\Maatch
     */
    protected function createMatch(array $properties): Maatch
    {
        return new Maatch($properties);
    }
}
