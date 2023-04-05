<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Api\ClubsApi;
use Cake\Console\Arguments;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Inflector;

/**
 * Club Command
 *
 * Display details about a club
 */
class ClubCommand extends VeoBaseCommand
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
            ->setDescription('Display details about a club')
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
        /** @var \Avolle\Veo\Entity\Club $club */
        $club = $this->tryApiResponse($io, fn () => $api->club($slug));

        if ($args->getOption('json')) {
            return $this->outputJsonFormatter($io, $club);
        }
        if ($args->getOption('debug')) {
            dd($club);
        }

        $table = [['Field', 'Value']];
        $fields = ['name', 'match_count', 'team_count', 'slug'];
        foreach ($fields as $field) {
            $table[] = [Inflector::humanize($field), $club->$field];
        }
        $io->helper('table')->output($table);

        return CommandInterface::CODE_SUCCESS;
    }
}
