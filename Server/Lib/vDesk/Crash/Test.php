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
    public const Crashed = "Crashed";

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
     * Information index for ratings about execution results of Tests and -cases.
     */
    public const Rating = "Rating";

    /**
     * Information index for execution duration of Tests and -cases.
     */
    public const Duration = "Duration";

    /**
     * Information index for applied values.
     */
    public const Values = "Values";

    /**
     * Information index for allocated memory of Tests and -cases.
     */
    public const Allocated = "Allocated";

    /**
     * Information index for allocated memory peak of Tests and -cases.
     */
    public const Peak = "Peak";

    /**
     * Information index for executed garbage collection cycles.
     */
    public const Cycles = "Cycles";

    /**
     * Information index for executed Test-cases.
     */
    public const Cases = "Cases";

    /**
     * Information index for error codes of Test-cases.
     */
    public const Code = "Code";

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
    final public function __invoke(): array {
        $Class = new \ReflectionClass(static::class);
        foreach($Class->getAttributes(Test\Skip::class) as $_) {
            return [self::Result => self::Skipped];
        }

        $Cases     = [];
        $Start     = 0;
        $Allocated = \memory_get_usage();
        $Previous  = \set_error_handler(null);
        $Assert    = static fn(string $File, int $Line, string $Assertion, ?string $Description = null): array => [
            self::Result    => self::Failed,
            self::Duration  => (\microtime(true) - $Start) * 1000,
            self::Allocated => \memory_get_usage() - $Allocated,
            self::Message   => $Description ?? "Assertion Failed",
            self::File      => $File,
            self::Line      => $Line,
            self::Trace     => $Assertion
        ];
        $Error     = static fn($Code, string $Message, string $File, int $Line, ?array $Context = []): array => [
            self::Result    => self::Crashed,
            self::Duration  => (\microtime(true) - $Start) * 1000,
            self::Allocated => \memory_get_usage() - $Allocated,
            self::Code      => $Code,
            self::Message   => $Message,
            self::File      => $File,
            self::Line      => $Line,
            self::Trace     => $Context
        ];
        $Summary   = static function(array $Cases): array {
            $Status = self::Success;
            $Amount = \max(1, \count($Cases));
            $Failed = \count(\array_filter($Cases, static fn(array $Case): bool => $Case[self::Result] === self::Failed));
            if((bool)$Failed) {
                $Status = self::Failed;
            }
            $Crashed = \count(\array_filter($Cases, static fn(array $Case): bool => $Case[self::Result] === self::Crashed));
            if((bool)$Crashed) {
                $Status = self::Crashed;
            }
            return [
                self::Result    => $Status,
                self::Rating    => 100 - (($Failed / $Amount) * 100 + ($Crashed / $Amount) * 100),
                self::Success   => $Amount - $Failed - $Crashed,
                self::Failed    => $Failed,
                self::Crashed   => $Crashed,
                self::Duration  => \array_reduce($Cases, static fn(float $Sum, array $Case): float => $Sum += $Case[self::Duration] ?? 0, 0),
                self::Allocated => \array_reduce($Cases, static fn(float $Sum, array $Case): float => $Sum += $Case[self::Allocated] ?? 0, 0),
                self::Peak      => \memory_get_peak_usage(),
                self::Cases     => $Cases
            ];
        };
        $Run       = function(\ReflectionMethod $Case, ...$Values) use ($Start, $Allocated): array {
            try {
                $Allocated = \memory_get_usage();
                $Start = \microtime(true);
                if($Case->isStatic()) {
                    static::{$Case->getName()}(...$Values);
                } else {
                    $this->{$Case->getName()}(...$Values);
                }
                $Result = [
                    self::Result    => self::Success,
                    self::Duration  => (\microtime(true) - $Start) * 1000,
                    self::Allocated => \memory_get_usage() - $Allocated,
                    self::Cycles    => \gc_collect_cycles()
                ];
                if(\count($Values) > 0) {
                    $Result[self::Values] = $Values;
                }
                return $Result;
            } catch(\Throwable $Exception) {
                return [
                    self::Result    => self::Crashed,
                    self::Duration  => (\microtime(true) - $Start) * 1000,
                    self::Allocated => \memory_get_usage() - $Allocated,
                    self::Code      => $Exception->getCode(),
                    self::Message   => $Exception->getMessage(),
                    self::File      => $Exception->getFile(),
                    self::Line      => $Exception->getLine(),
                    self::Trace     => $Exception->getTrace()
                ];
            }
        };

        //Run test cases.
        foreach($Class->getMethods() as $Method) {
            //Skip magic methods.
            if(
                $Method->isAbstract()
                || $Method->isInternal()
                || $Method->isConstructor()
                || $Method->isDestructor()
                || \str_starts_with($Method->getName(), "__")
            ) {
                continue;
            }

            //Skip Test case.
            foreach($Method->getAttributes(Test\Case\Skip::class) as $Attribute) {
                $Cases[$Method->getName()] = [self::Result => self::Skipped];
                continue 2;
            }

            //Set error handler.
            \assert_options(\ASSERT_CALLBACK, static fn(...$Values) => $Cases[$Method->getName()] = $Assert(...$Values));
            \set_error_handler(static fn(...$Values) => $Cases[$Method->getName()] = $Error(...$Values));

            //Repeat Test case.
            foreach($Method->getAttributes(Test\Case\Repeat::class) as $Attribute) {
                $Repetitions = [];
                foreach(Test\Case\Repeat::FromReflector($Attribute) as $Repetition) {
                    //Overwrite error handlers.
                    \assert_options(\ASSERT_CALLBACK, static fn(...$Values) => $Repetitions[$Repetition] = $Assert(...$Values));
                    \set_error_handler(static fn(...$Values) => $Repetitions[$Repetition] = $Error(...$Values));

                    $Repetitions[$Repetition] = $Run($Method);
                }
                $Cases[$Method->getName()] = $Summary($Repetitions);
                continue 2;
            }

            //Crash Test case.
            foreach($Method->getAttributes(Test\Case\Crash::class) as $Attribute) {
                $Repetitions = [];
                foreach(Test\Case\Crash::FromReflector($Attribute) as $Repetition => $Values) {
                    //Overwrite error handlers.
                    \assert_options(\ASSERT_CALLBACK, static fn(...$Values) => $Repetitions[$Repetition] = $Assert(...$Values));
                    \set_error_handler(static fn(...$Values) => $Repetitions[$Repetition] = $Error(...$Values));

                    $Repetitions[$Repetition] = $Run($Method, ...$Values);
                }
                $Cases[$Method->getName()] = $Summary($Repetitions);
                continue 2;
            }

            //Apply values.
            foreach($Method->getAttributes(Test\Case\Values::class) as $Attribute) {
                $Cases[$Method->getName()] = $Run($Method, ...Test\Case\Values::FromReflector($Attribute)->Values);
                continue 2;
            }

            //Normal run.
            $Cases[$Method->getName()] = $Run($Method);
        }

        //Restore previous error handler.
        \set_error_handler($Previous);

        return $Summary($Cases);
    }

}