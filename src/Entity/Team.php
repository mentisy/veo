<?php
declare(strict_types=1);

namespace Avolle\Veo\Entity;

/**
 * Team entity
 */
class Team extends Entity
{
    /**
     * Id
     *
     * @var string
     */
    public string $id;

    /**
     * How many matches the team has
     *
     * @var int
     */
    public int $match_count;

    /**
     * How many members the team has
     *
     * @var int
     */
    public int $member_count;

    /**
     * Not sure
     *
     * @var string|null
     */
    public ?string $user_member;

    /**
     * Name of team
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
     * Short name
     *
     * @var string
     */
    public string $short_name;

    /**
     * Short name displayed
     *
     * @var string
     */
    public string $short_name_display;

    /**
     * Slug
     *
     * @var string
     */
    public string $slug;

    /**
     * Image URL
     *
     * @var string|null
     */
    public ?string $image;

    /**
     * Veo URL
     *
     * @var string
     */
    public string $url;

    /**
     * Gender of team
     *
     * @var string
     */
    public string $gender;

    /**
     * Age group of team
     *
     * @var string
     */
    public string $age_group;

    /**
     * Club details
     *
     * @var array
     */
    public array $club;

    /**
     * Header image URL
     *
     * @var string|null
     */
    public ?string $header_image;
}
