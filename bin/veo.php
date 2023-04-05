#!/usr/bin/php -q
<?php

use Avolle\Veo\Application;
use Cake\Console\CommandRunner;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/paths.php';

$runner = new CommandRunner(new Application(), 'veo');
exit($runner->run($argv));
