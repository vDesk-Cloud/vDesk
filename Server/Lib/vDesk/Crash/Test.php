<?php
declare(strict_types=1);

namespace vDesk\Crash;

/**
 * Abstract baseclass for test cases.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Test {

    /**
     * Result indicating whether a case of the Test has crashed while being executed.
     */
    public const Crashed = "Cashed";

    /**
     * Result indicating whether an assertion of a case of the Test has failed.
     */
    public const Failed = "Failed";

    /**
     * Result indicating whether a case of the Test has been successfully executed.
     */
    public const Success = "Success";

    /**
     * Result indicating whether the Test has been skipped.
     */
    public const Skipped = "Skipped";

    /**
     * Information index for execution results Test-case.
     */
    public const Result = "Result";

    /**
     * Information index for execution duration of Tests and -cases.
     */
    public const Duration = "Duration";

    /**
     * Information index for allocated memory of Tests and -cases.
     */
    public const Allocated = "Allocated";

    /**
     * Information index for error messages of Test-cases.
     */
    public const Message = "Message";

    /**
     * Information index for file names of error messages of Test-cases.
     */
    public const File = "File";

    /**
     * Information index for line numbers of error messages of Test-cases.
     */
    public const Line = "Line";

    /**
     * Information index for stack traces of Test-cases.
     */
    public const Trace = "Trace";

    /**
     * Runs the cases of the Test and creates an array containing information about the Test and its executed cases.
     *
     * @return array An array containing information about the Test and its executed cases.
     */
    final public function Run(): array {
        //Save previous error handler.
        $Previous = \set_error_handler(null);

        //Run test cases.
        $Cases = [];
        $Usage = \memory_get_usage();
        foreach(\get_class_methods(static::class) as $Case) {
            //Skip magic methods.
            if(\str_starts_with($Case, "__")) {
                continue;
            }
            $Start     = \microtime(true);
            $Allocated = \memory_get_usage();
            \assert_options(
                \ASSERT_CALLBACK,
                static fn(string $File, int $Line, string $Assertion, ?string $Description = null) => $Cases[$Case] = [
                    self::Result    => self::Failed,
                    self::Duration  => \round((\microtime(true) - $Start) * 1000),
                    self::Allocated => \memory_get_usage() - $Allocated,
                    self::Message   => $Description ?? "Assertion Failed",
                    self::File      => $File,
                    self::Line      => $Line,
                    self::Trace     => $Assertion
                ]
            );
            \set_error_handler(
                static fn($Code, $Message, $File, $Line, $Context = []) => $Cases[$Case] = [
                    self::Result    => self::Crashed,
                    self::Duration  => \round((\microtime(true) - $Start) * 1000),
                    self::Allocated => \memory_get_usage() - $Allocated,
                    self::Message   => $Message,
                    self::File      => $File,
                    self::Line      => $Line,
                    self::Trace     => $Context
                ]
            );
            //Execute test case.
            try {
                $this->{$Case}();
                $Cases[$Case] = [
                    self::Result    => self::Success,
                    self::Duration  => \round((\microtime(true) - $Start) * 1000),
                    self::Allocated => \memory_get_usage() - $Allocated,
                ];
            } catch(\Throwable $Exception) {
                $Cases[$Case] = [
                    self::Result    => self::Crashed,
                    self::Duration  => \round((\microtime(true) - $Start) * 1000),
                    self::Allocated => \memory_get_usage() - $Allocated,
                    self::Message   => $Exception->getMessage(),
                    self::File      => $Exception->getFile(),
                    self::Line      => $Exception->getLine(),
                    self::Trace     => $Exception->getTrace()
                ];
            }
        }

        //Restore previous error handler.
        \set_error_handler($Previous);

        //Aggregate result information.
        return [
            self::Duration  => \array_reduce($Cases, static fn(float $Sum, array $Case): float => $Sum += $Case[self::Duration], 0),
            self::Allocated => \memory_get_usage() - $Usage,
            "Rating"        => (\count($Cases) / \count(\array_filter(
                        $Cases,
                        static fn(array $Case): bool => $Case[self::Result] === self::Crashed || $Case[self::Result] === self::Failed
                    ))) * 100,
            "Cases"         => $Cases
        ];
    }

}