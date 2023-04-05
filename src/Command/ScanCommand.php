<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Api\MatchesApi;
use Cake\Chronos\Chronos;
use Cake\Console\Arguments;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Scan Command
 *
 * Scan for matches that fit certain keywords, like cameras and team names
 */
class ScanCommand extends VeoBaseCommand
{
    /**
     * The maximum amount of pages one is allowed to scan through one execution.
     */
    protected const MAX_PAGE = 100;

    /**
     * Hook method for defining this command's option parser.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription('Scan for matches that fit certain keywords, like cameras and team names')
            ->addOption('session', ['short' => 's', 'help' => 'Your Veo session id'])
            ->addOption('back-to', ['short' => 'b', 'help' => 'At what datetime to scan back to'])
            ->addOption('start', ['short' => 'p', 'help' => 'Page to start at'])
            ->addOption('end', ['short' => 'e', 'help' => 'Page to end at']);
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
        // Get options
        $sessionId = $this->resolveOption($args, $io, 'session', 'Session ID:', Configure::read('session'));
        $scanBackTo = $this->resolveOption($args, $io, 'back-to', 'What time to scan back to?');
        $pageStart = $args->getOption('start');
        $pageEnd = $args->getOption('end');

        // Setup
        $scanBackTo = new Chronos($scanBackTo, Configure::read('timezone'));
        $maxPage = $pageEnd ? (int)$pageEnd : self::MAX_PAGE;
        $api = new MatchesApi($sessionId);
        $api->useEssentialFields();

        $totalMatchesFound = 0;
        // Scan
        $pageStart = $pageStart ? (int)$pageStart : 1;
        $pageEnd = $pageEnd ? (int)$pageEnd : ($pageStart + $maxPage - 1);
        for ($page = $pageStart; $page <= $pageEnd; $page++) {
            $io->info('Scanning page ' . $page);

            /** @var array<\Avolle\Veo\Entity\Maatch> $result */
            $result = $this->tryApiResponse($io, fn () => $api->matches($page), '{error} at page ' . $page);
            $filteredMatches = $this->filteredMatches($result);
            $totalMatchesFound += count($filteredMatches);

            $io->info(sprintf('Result returned %s matches.', count($result)));
            if (empty($filteredMatches)) {
                $io->warning('Filter returned zero matches');
            } else {
                $io->info(sprintf('First match of result created %s', $result[0]->created('d. M Y H:i')));
                $io->success(sprintf('Filter returned %s matches.', count($filteredMatches)));
                $table = [['Page', 'Title', 'Download', 'Slug']];
                foreach ($filteredMatches as $match) {
                    $table[] = [$page, $match->title, $match->downloadLink(), $match->slug];
                }
                $io->helper('Table')->output($table);
            }
            $io->hr();
        }

        $io->info(sprintf('Found a total of %s filtered matches', $totalMatchesFound));

        return CommandInterface::CODE_SUCCESS;
    }

    /**
     * Filter matches to contain the configured cameras and team names
     *
     * @param array<\Avolle\Veo\Entity\Maatch> $result Unfiltered API result of matches
     * @return array<\Avolle\Veo\Entity\Maatch>
     */
    protected function filteredMatches(array $result): array
    {
        $cameras = Configure::read('cameras');
        $teams = collection(Configure::read('teams'));
        $matched = [];
        foreach ($result as $match) {
            $cameraFound = in_array($match->camera, $cameras);
            $teamFound = $teams
                    ->filter(fn ($team) => str_contains(strtolower($match->title), strtolower($team)))
                    ->count() > 0;

            if ($cameraFound || $teamFound) {
                $matched[] = $match;
            }
        }

        return $matched;
    }
}
