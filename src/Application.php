<?php
declare(strict_types=1);

namespace Avolle\Veo;

use Avolle\Veo\Command\ClubCommand;
use Avolle\Veo\Command\MatchCommand;
use Avolle\Veo\Command\MatchesCommand;
use Avolle\Veo\Command\PageCommand;
use Avolle\Veo\Command\ScanCommand;
use Avolle\Veo\Command\ScanTeams;
use Avolle\Veo\Command\SearchClubsCommand;
use Avolle\Veo\Command\TeamsCommand;
use Cake\Console\CommandCollection;
use Cake\Core\Configure;
use Cake\Core\ConsoleApplicationInterface;

/**
 * Application class
 */
class Application implements ConsoleApplicationInterface
{
    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        Configure::write(require ROOT . 'config/config.php');
    }

    /**
     * Define the console commands for the application.
     *
     * @param \Cake\Console\CommandCollection $commands The CommandCollection to add commands into.
     * @return \Cake\Console\CommandCollection The updated collection.
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        $commands->add('club', ClubCommand::class);
        $commands->add('match', MatchCommand::class);
        $commands->add('matches', MatchesCommand::class);
        $commands->add('page', PageCommand::class);
        $commands->add('scan', ScanCommand::class);
        $commands->add('scanTeams', ScanTeams::class);
        $commands->add('searchClubs', SearchClubsCommand::class);
        $commands->add('teams', TeamsCommand::class);

        return $commands;
    }
}
