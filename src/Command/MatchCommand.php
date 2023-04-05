<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Api\MatchesApi;
use Cake\Console\Arguments;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Inflector;

/**
 * Match Command
 *
 * Display metadata about a specific match
 */
class MatchCommand extends VeoBaseCommand
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
            ->setDescription('Display metadata about a specific match')
            ->addOption('video-slug', ['short' => 'v', 'help' => 'Video slug'])
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
        $slug = $this->resolveOption($args, $io, 'video-slug', 'Video slug:');

        $api = new MatchesApi();
        /** @var \Avolle\Veo\Entity\Maatch $match */
        $match = $this->tryApiResponse($io, fn () => $api->match($slug));

        if ($args->getOption('json')) {
            return $this->outputJsonFormatter($io, $match);
        }
        if ($args->getOption('debug')) {
            dd($match);
        }

        $table = [['Field', 'Value']];
        foreach ($this->fields() as $field) {
            $table[] = [Inflector::humanize($field), $match->$field];
        }
        $table[] = ['Status', $match->getProcessingStatus()];
        $table[] = ['Created', $match->dateTime()];
        $table[] = ['Download', $match->downloadLink()];

        $io->helper('table')->output($table);

        return CommandInterface::CODE_SUCCESS;
    }

    /**
     * Fields to display in table
     *
     * @return array<string>
     */
    public function fields(): array
    {
        return [
            'title',
            'camera',
            'privacy',
            'slug',
        ];
    }
}
