<?php
declare(strict_types=1);

namespace Avolle\Veo\Entity;

use Cake\Chronos\Chronos;

/**
 * Match entity
 */
class Maatch extends Entity
{
    /**
     * Template for URL to download match
     */
    protected const DOWNLOAD_TEMPLATE = 'https://download.veocdn.com/{identifier}/standard/'
        . 'machine/{machine}/video.mp4?name={name}.mp4';

    /**
     * Template for URL to watch match
     */
    protected const WATCH_TEMPLATE = 'https://app.veo.co/matches/{slug}/';

    /**
     * Finished status for processing
     */
    public const FINISHED = 'Finished';

    /**
     * Default format to output created field
     */
    public const DEFAULT_CREATED_FORMAT = 'Y-m-d H:i:s';

    /**
     * Camera
     *
     * @var string|null
     */
    public ?string $camera = null;

    /**
     * Datetime match was created
     *
     * @var string|null
     */
    public ?string $created = null;

    /**
     * Duration of match
     *
     * @var float|null
     */
    public ?float $duration = null;

    /**
     * Identifier
     *
     * @var string|null
     */
    public ?string $identifier = null;

    /**
     * Not sure
     *
     * @var bool|null
     */
    public ?bool $is_paid_for_upload_minutes = null;

    /**
     * Is this a shared recording
     *
     * @var bool|null
     */
    public ?bool $is_shared_recording = null;

    /**
     * Permissions details
     *
     * @var array|null
     */
    public ?array $permissions = null;

    /**
     * Privacy (private/public)
     *
     * @var string|null
     */
    public ?string $privacy = null;

    /**
     * Processing status details
     *
     * @var array|null
     */
    public ?array $processing_status = null;

    /**
     * Slug
     *
     * @var string|null
     */
    public ?string $slug = null;

    /**
     * Start datetime
     *
     * @var string|null
     */
    public ?string $start = null;

    /**
     * Team details
     *
     * @var array|null
     */
    public ?array $team = null;

    /**
     * Thumbnail
     *
     * @var string|null
     */
    public ?string $thumbnail = null;

    /**
     * Title
     *
     * @var string|null
     */
    public ?string $title = null;

    /**
     * URL
     *
     * @var string|null
     */
    public ?string $url = null;

    /**
     * Output the created field in the provided format.
     *
     * @param string|null $format Chronos format string
     * @return string
     */
    public function created(?string $format = null): string
    {
        return Chronos::parse($this->created)->format($format ?? self::DEFAULT_CREATED_FORMAT);
    }

    /**
     * Get the processing status.
     *
     * @param bool $label Whether to display label (human-friendly) or status (machine-friendly)
     * @return string
     */
    public function getProcessingStatus(bool $label = true): string
    {
        $key = $label ? 'label' : 'status';

        return $this->processing_status[$key] ?? self::FINISHED;
    }

    /**
     * Get  a URL to download the match.
     *
     * @return string
     */
    public function downloadLink(): string
    {
        $identifier = $this->identifier;
        $machine = $this->getMachine();
        if (empty($machine)) {
            return 'No machine found';
        }
        $name = urlencode($this->title);

        return str_replace(
            ['{identifier}', '{machine}', '{name}'],
            [$identifier, $machine, $name],
            self::DOWNLOAD_TEMPLATE,
        );
    }

    /**
     * Get a URL to watch the match
     *
     * @return string
     */
    public function watchLink(): string
    {
        return str_replace('{slug}', $this->slug, self::WATCH_TEMPLATE);
    }

    /**
     * Try to find the machine hex amonst the thumbnail string.
     * The machine hex is required for downloading the match, as it is part of the download url.
     *
     * @return string|null
     */
    public function getMachine(): ?string
    {
        if (empty($this->thumbnail)) {
            return null;
        }
        if (preg_match(':/machine/([0-9a-z]+)/:', $this->thumbnail, $matches) !== false) {
            if (count($matches) < 2) {
                return null;
            }
            [, $machine] = $matches;

            return $machine;
        }

        return null;
    }

    /**
     * Get created field in a format that displays date
     *
     * @return string
     */
    public function date(): string
    {
        return $this->created('d. M Y');
    }

    /**
     * Get created field in a format that displays both date and time
     *
     * @return string
     */
    public function dateTime(): string
    {
        return $this->created('d. M Y H:i:s');
    }
}
