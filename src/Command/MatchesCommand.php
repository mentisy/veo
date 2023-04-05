<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Api\MatchesApi;
use Cake\Console\Arguments;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Matches Command
 *
 * Display matches for a club and team (latter if specified)
 */
class MatchesCommand extends VeoBaseCommand
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
            ->setDescription('Display matches for a club and team (latter if specified)')
            ->addOption('club', ['short' => 'c', 'help' => 'Club slug'])
            ->addOption('team', ['short' => 't', 'help' => 'Team slug'])
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
        $club = $this->resolveOption($args, $io, 'club', 'Club slug:');
        $team = $this->resolveOption($args, $io, 'team', 'Team slug:', null, false);

        $msg = "Getting matches for $club";
        if (!empty($team)) {
            $msg .= " and $team";
        }
        $io->info($msg);

        $api = new MatchesApi();
        $api->useEssentialFields();
        /** @var array<\Avolle\Veo\Entity\Maatch> $matches */
        $matches = $this->tryApiResponse($io, fn () => $api->clubTeamMatches($club, $team));

        if ($args->getOption('json')) {
            return $this->outputJsonFormatter($io, $matches);
        }
        if ($args->getOption('debug')) {
            dd($matches);
        }

        $table = [['Date', 'Title', 'Download', 'Watch']];
        foreach ($matches as $match) {
            $table[] = [$match->date(), $match->title, $match->downloadLink(), $match->watchLink()];
        }

        $io->helper('Table')->output($table);

        return CommandInterface::CODE_SUCCESS;
    }
}
