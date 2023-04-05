<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Api\MatchesApi;
use Cake\Console\Arguments;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Page Command
 *
 * Display matches for a specific page
 */
class PageCommand extends VeoBaseCommand
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
            ->setDescription('Display matches for a specific page')
            ->addOption('session', ['short' => 's', 'help' => 'Your Veo session id'])
            ->addOption('page', ['short' => 'p', 'help' => 'Page to get'])
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
        $sessionId = $this->resolveOption($args, $io, 'session', 'Session ID:', Configure::read('session'));
        $page = $this->resolveOption($args, $io, 'page', 'Page to get:', '1');

        $api = new MatchesApi($sessionId);
        $api->useEssentialFields();
        /** @var array<\Avolle\Veo\Entity\Maatch> $result */
        $result = $this->tryApiResponse($io, fn () => $api->matches((int)$page));

        if ($args->getOption('json')) {
            return $this->outputJsonFormatter($io, $result);
        }
        if ($args->getOption('debug')) {
            dd($result);
        }

        $table = [['Date', 'Camera', 'Title', 'Status', 'Download']];
        foreach ($result as $match) {
            $created = $match->created('d. M Y H:i');
            $table[] = [$created, $match->camera, $match->title, $match->getProcessingStatus(), $match->downloadLink()];
        }

        $io->helper('Table')->output($table);

        return CommandInterface::CODE_SUCCESS;
    }
}
