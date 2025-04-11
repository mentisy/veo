<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Api\ClubsApi;
use Avolle\Veo\Exception\VeoApiException;
use Cake\Console\Arguments;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Search clubs Command
 *
 * Try to find clubs that have Veo
 */
class SearchClubsCommand extends VeoBaseCommand
{
    /**
     * Variations of things to append to club slug search
     */
    public const VARIATIONS = [
        '%s',
        '%s-il',
        '%s-idrettslag',
        '%s-idrottslag',
        '%s-idrotslag',
        '%s-idrott',
        '%s-idrot',
        '%s-fotball',
        '%s-il-fotball',
        '%s-turn-og-idrettslag',
    ];

    /**
     * Hook method for defining this command's option parser.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription('Try to find clubs with Veo')
            ->addOption('clubs', ['short' => 'c', 'help' => 'List of clubs to search (slugged, comma separated)'])
            ->addOption('no_variations', [
                'short' => 'n',
                'help' => 'Will not search variations of provided slug',
                'boolean' => true,
            ])
            ->addOption('json', ['short' => 'j', 'boolean' => true, 'help' => 'Output a JSON Formatter link'])
            ->addOption('debug', ['short' => 'd', 'boolean' => true, 'help' => 'Output by dump and die']);
    }

    /**
     * Execute command
     *
     * @param \Cake\Console\Arguments $args Command arguments
     * @param \Cake\Console\ConsoleIo $io Console Input/Output
     * @return int Exit code
     * @throws \Exception
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $slugs = $this->resolveOption($args, $io, 'clubs', 'Club slugs (comma separated):');
        $slugsArray = explode(',', $slugs);
        $slugsArray = array_map('mb_strtolower', $slugsArray);

        $api = new ClubsApi();

        $foundClubs = [];
        foreach ($slugsArray as $slug) {
            $slug = trim($slug, ' ');
            $search = static::VARIATIONS;
            if ($args->getOption('no_variations')) {
                $search = [$slug];
            }
            foreach ($search as $variation) {
                $clubVariation = sprintf($variation, $slug);
                try {
                    $io->info(sprintf('Scanning %s', $clubVariation));
                    $club = $api->club($clubVariation);
                } catch (VeoApiException) {
                    continue;
                }
                $foundClubs[] = $club;
            }
        }

        if ($args->getOption('json')) {
            return $this->outputJsonFormatter($io, $foundClubs);
        }
        if ($args->getOption('debug')) {
            dd($foundClubs);
        }

        if (empty($foundClubs)) {
            return $io->warning('No clubs found');
        }

        $table = [['Title', 'Matches', 'Teams', 'Slug']];
        foreach ($foundClubs as $club) {
            $table[] = [$club->title, $club->match_count, $club->team_count, $club->slug];
        }
        $io->helper('table')->output($table);

        return CommandInterface::CODE_SUCCESS;
    }
}
