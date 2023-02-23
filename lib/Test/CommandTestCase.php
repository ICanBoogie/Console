<?php

namespace ICanBoogie\Console\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LazyCommand;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Tester\CommandTester;

use function array_map;
use function ICanBoogie\app;
use function implode;
use function preg_quote;

abstract class CommandTestCase extends TestCase
{

    /**
     * @dataProvider provideExecute
     *
     * @param class-string<Command> $command_class
     * @param string[] $input
     * @param string[] $row
     */
    public function testExecute(
        string $command_name,
        string $command_class,
        array $input = [],
        array $row = null,
        string $regex = null,
    ): void {
        $tester = $this->getTester($command_name, $command_class);
        $tester->execute($input);

        $tester->assertCommandIsSuccessful();
        $display = $tester->getDisplay();

        if ($row) {
            $this->assertDisplayContainsRow($row, $display);
        }

        if ($regex) {
            $this->assertMatchesRegularExpression($regex, $display);
        }
    }

    /**
     * @return array<array{ string, class-string, 2?: string[], 3?: string[], 4?: string|null }>
     */
    abstract public static function provideExecute(): array;

    /**
     * Returns the command loader.
     */
    public static function getCommandLoader(): CommandLoaderInterface
    {
        return app()->service_for_id('console.command_loader', CommandLoaderInterface::class);
    }

    /**
     * Returns a tester for a command.
     *
     * @template T of object
     *
     * @param class-string<T> $command_class
     */
    public static function getTester(string $command_name, string $command_class): CommandTester
    {
        $loader = self::getCommandLoader();
        $command = $loader->get($command_name);

        if ($command instanceof LazyCommand) {
            $command = $command->getCommand();
        }

        self::assertInstanceOf($command_class, $command);

        return new CommandTester($command);
    }

    /**
     * @param string[] $row
     */
    public static function assertDisplayContainsRow(array $row, string $display): void
    {
        $delimiter = '/';
        $regexp = $delimiter
            . implode('\s+\|\s+', array_map(fn(string $s) => preg_quote($s, $delimiter), $row))
            . $delimiter;

        self::assertMatchesRegularExpression($regexp, $display);
    }
}
