<?php
declare(strict_types=1);

namespace Avolle\Veo\Command;

use Avolle\Veo\Exception\VeoApiException;
use Cake\Console\Arguments;
use Cake\Console\BaseCommand;
use Cake\Console\ConsoleIo;

/**
 * Extended Cake Console Base Command
 *
 * Provides convenience methods for commands
 */
abstract class VeoBaseCommand extends BaseCommand
{
    /**
     * Error template for outputing Veo exception messages.
     */
    private const ERROR_TEMPLATE = '{error}';

    /**
     * Base URL to JSON formatter. Allows outputting result to a browser displaying JSON.
     */
    private const JSON_FORMATTER_URL = 'https://jsonformatter.curiousconcept.com/?data=';

    /**
     * Try an API call, and if a VeoException occurs, output the error message to the console.
     *
     * @param \Cake\Console\ConsoleIo $io Console Input/Output.
     * @param callable $callable API Call to make.
     * @param string|null $message Error message to override default. Use {error} to include the exception message.
     * @return mixed Either the returned API call or in the case of an error, it will return number of bytes of message.
     */
    public function tryApiResponse(ConsoleIo $io, callable $callable, ?string $message = null): mixed
    {
        try {
            return $callable();
        } catch (VeoApiException $exception) {
            if (empty($message)) {
                $message = $exception->getMessage();
            } else {
                $message = str_replace(self::ERROR_TEMPLATE, $exception->getMessage(), $message);
            }

            return $io->error($message);
        }
    }

    /**
     * Output variable content as a json encoded url string, so it can be viewed in a browser.
     *
     * @param \Cake\Console\ConsoleIo $io Console Input/Output
     * @param mixed $what What to output
     * @return int|null
     */
    public function outputJsonFormatter(ConsoleIo $io, mixed $what): ?int
    {
        $output = self::JSON_FORMATTER_URL;
        $output .= urlencode(json_encode($what) ?: '');

        return $io->info($output);
    }

    /**
     * Resolve the argument option. If no option is provided, ask the user for the question through a console prompt.
     * If $requireAnswer is true, it will not accept an empty answer. It will thus re-ask until a non-empty
     * answer is given.
     *
     * @param \Cake\Console\Arguments $args Console Arguments
     * @param \Cake\Console\ConsoleIo $io Console Input/Output
     * @param string $option Option name
     * @param string $question Question to prompt if option is empty
     * @param string|null $default Default answer to provide user when prompting user
     * @param bool $requireAnswer Require an answer for the prompt. Will re-ask if answer is empty.
     * @return string Response from user, either from option or answer to prompt.
     */
    public function resolveOption(
        Arguments $args,
        ConsoleIo $io,
        string $option,
        string $question,
        ?string $default = null,
        bool $requireAnswer = true,
    ): string {
        if ($args->hasOption($option)) {
            return (string)$args->getOption($option);
        }
        do {
            $answer = $io->ask($question, $default);
        } while (empty($answer) && $requireAnswer);

        return $answer;
    }
}
