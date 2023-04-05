<?php
declare(strict_types=1);

namespace Avolle\Veo\Entity;

/**
 * Club entity
 */
class Club extends Entity
{
    /**
     * Club entitlements
     *
     * @var string|null
     */
    public ?string $club_entitlements;

    /**
     * Common name of club
     *
     * @var string
     */
    public string $common_name;

    /**
     * Country details
     *
     * @var array
     */
    public array $country;

    /**
     * Crest URL
     *
     * @var string|null
     */
    public ?string $crest;

    /**
     * Not sure
     *
     * @var string|null
     */
    public ?string $external_id_dbu;

    /**
     * Header image URL
     *
     * @var string|null
     */
    public ?string $header_image;

    /**
     * ID
     *
     * @var string
     */
    public string $id;

    /**
     * Support id
     *
     * @var string|null
     */
    public ?string $support_id;

    /**
     * Whether the person requesting the details is a club admin or not
     *
     * @var bool
     */
    public bool $is_club_admin;

    /**
     * Whether the person requesting the details is following the club
     *
     * @var bool
     */
    public bool $is_following;

    /**
     * Not sure
     *
     * @var bool
     */
    public bool $is_bosbury;

    /**
     * How many matches the club has
     *
     * @var int
     */
    public int $match_count;

    /**
     * Name of club
     *
     * @var string
     */
    public string $name;

    /**
     * Permission details
     *
     * @var array
     */
    public array $permissions;

    /**
     * Short name of club
     *
     * @var string
     */
    public string $short_name;

    /**
     * Slug
     *
     * @var string
     */
    public string $slug;

    /**
     * How many teams the club has
     *
     * @var int
     */
    public int $team_count;

    /**
     * Title of club
     *
     * @var string
     */
    public string $title;

    /**
     * Veo club URL
     *
     * @var string
     */
    public string $url;
}
