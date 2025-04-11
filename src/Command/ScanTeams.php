<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Api\MatchesApi;
use Avolle\Veo\Entity\Maatch;
use Cake\Chronos\Chronos;
use Cake\Console\Arguments;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Unknown command
 *
 * Display all matches for the configured teams in a specified timeline
 */
class ScanTeams extends VeoBaseCommand
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription('Display all matches for the configured teams in a specified timeline')
            ->addOption('from', ['short' => 'f', 'help' => 'Matches from'])
            ->addOption('to', ['short' => 't', 'help' => 'Matches to'])
            ->addOption('clubOnly', ['short' => 'c', 'boolean' => true, 'help' => 'Only search by club slug'])
            ->addOption('json', ['short' => 'j', 'boolean' => true, 'help' => 'Output a JSON Formatter link'])
            ->addOption('debug', ['short' => 'd', 'boolean' => true, 'help' => 'Output by dump and die']);
    }

    /**
     * Execute command
     *
     * @param \Cake\Console\Arguments $args Command arguments
     * @param \Cake\Console\ConsoleIo $io Console Input/Output
     * @return int Exit code
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $from = $this->resolveOption($args, $io, 'from', 'From when:', 'yesterday');
        $to = $this->resolveOption($args, $io, 'to', 'To when:', 'today');
        $clubOnly = $args->getOption('clubOnly');

        $teams = Configure::read('teamsFull');
        $io->info(sprintf('Found %s teams', count($teams)));

        $fromParsed = Chronos::parse($from);
        $toParsed = Chronos::parse($to);

        $io->info(sprintf(
            'Scanning for matches between %s and %s.',
            $fromParsed->format('d. M Y'),
            $toParsed->format('d. M Y'),
        ));

        $api = new MatchesApi();
        $api->useEssentialFields();
        $matches = collection([]);
        foreach ($teams as $team) {
            $io->info(sprintf("Fetching %s", $team['name']));
            $start = microtime(true);
            $teamSlug = $clubOnly ? null : $team['team_slug'];

            $teamMatches = $this->tryApiResponse(
                $io,
                fn () => $api->clubTeamMatches($team['club_slug'], $teamSlug),
            );
            if (is_int($teamMatches)) {
                $io->out('Skipping since API request or response was invalid.');
                continue;
            }
            $end = microtime(true);
            $time = (int)(($end-$start) * 1000);
            $io->verbose(sprintf('Request took %s ms', $time));
            $teamMatches = collection($teamMatches)->filter(function (Maatch $match) use ($fromParsed, $toParsed) {
                $created = Chronos::parse($match->created);

                return $created->between($fromParsed, $toParsed);
            });
            if (!$teamMatches->isEmpty()) {
                $io->success(sprintf('Found %s matches', $teamMatches->count()));
            }
            $matches = $matches->append($teamMatches);
            sleep(1);
        }
        /** @var \Cake\Collection\CollectionInterface|array<\Avolle\Veo\Entity\Maatch> $matches */
        $matches = $matches->toList();

        if ($args->getOption('json')) {
            return $this->outputJsonFormatter($io, $matches);
        }
        if ($args->getOption('debug')) {
            dd($matches);
        }

        if (empty($matches)) {
            return $io->warning('No matches found.');
        }

        $table = [['Date', 'Club', 'Team', 'Title', 'Status', 'Download', 'Watch']];
        foreach ($matches as $match) {
            $table[] = [
                $match->date(),
                $match->team?->club?->title ?? '',
                $match->team?->name ?? '',
                $match->title,
                $match->processing_status->label(),
                $match->downloadLink(),
                $match->watchLink(),
            ];
        }

        $io->helper('Table')->output($table);

        return CommandInterface::CODE_SUCCESS;
    }
}
