<?php
declare(strict_types=1);

namespace Avolle\Veo\Api;

use Avolle\Veo\Entity\Club;
use Avolle\Veo\Entity\Team;

/**
 * Clubs API class
 *
 * Requests related to clubs.
 */
class ClubsApi extends VeoApi
{
    /**
     * Base path for clubs API requests
     */
    protected const BASE_PATH = 'app/clubs';

    /**
     * Get details for the specified club
     *
     * @param string $slug Club slug.
     * @return \Avolle\Veo\Entity\Club
     * @throws \Avolle\Veo\Exception\VeoApiException
     */
    public function club(string $slug): Club
    {
        $response = $this->client->get($slug);
        $club = $this->convertResponse($response);

        return $this->createClub($club);
    }

    /**
     * Get teams for the specified club.
     *
     * @return array<\Avolle\Veo\Entity\Team>
     * @throws \Avolle\Veo\Exception\VeoApiException
     */
    public function teams(string $slug): array
    {
        $response = $this->client->get($slug . '/teams/');
        $teams = $this->convertResponse($response);

        return array_map([$this, 'createTeam'], $teams);
    }

    /**
     * Create a club entity with the given properties
     *
     * @param array $properties Club properties from array
     * @return \Avolle\Veo\Entity\Club
     */
    protected function createClub(array $properties): Club
    {
        return new Club($properties);
    }

    /**
     * Create a team entity with the given properties.
     *
     * @param array $properties Team properties from array
     * @return \Avolle\Veo\Entity\Team
     */
    protected function createTeam(array $properties): Team
    {
        return new Team($properties);
    }
}
