<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Api\ClubsApi;
use Cake\Console\Arguments;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Teams Command
 *
 * Display teams for a given club
 */
class TeamsCommand extends VeoBaseCommand
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
            ->setDescription('Display teams for a given club')
            ->addOption('club-slug', ['short' => 'c', 'help' => 'Club slug'])
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
        $slug = $this->resolveOption($args, $io, 'club-slug', 'Club slug:');

        $api = new ClubsApi();
        /** @var array<\Avolle\Veo\Entity\Team> $teams */
        $teams = $this->tryApiResponse($io, fn () => $api->teams($slug));

        if ($args->getOption('json')) {
            return $this->outputJsonFormatter($io, $teams);
        }
        if ($args->getOption('debug')) {
            dd($teams);
        }

        if (empty($teams)) {
            return $io->warning('Club has no teams');
        }
        $table = [['Name', 'Slug', 'Matches', 'Members', 'Age', 'Gender']];
        foreach ($teams as $team) {
            $table[] = [
                $team->name,
                $team->slug,
                $team->match_count,
                $team->member_count,
                $team->age_group,
                $team->gender,
            ];
        }
        $io->helper('table')->output($table);

        return CommandInterface::CODE_SUCCESS;
    }
}
