<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Crash\Test;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\IO\RecursiveFilesystemInfoIterator;
use vDesk\Modules\Command;

/**
 * Crash\Test Module class.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Crash extends \vDesk\Modules\Module {

    /**
     * @param null|string $Path
     *
     * @return array
     */
    public static function Test(?string $Path = null): array {
        $Path ??= Command::$Parameters["Path"] ?? \Server
                                                  . Path::Separator . "Lib"
                                                  . Path::Separator . "vDesk"
                                                  . Path::Separator . "Crash"
                                                  . Path::Separator . "Tests";

        //Search for class files.
        $Tests = [];
        /** @var FileInfo $FilesystemInfo */
        foreach(new RecursiveFilesystemInfoIterator(new DirectoryInfo($Path)) as $FilesystemInfo) {
            if($FilesystemInfo->Extension === "php") {
                $Class = "\\vDesk\\Crash\\Tests" .
                         \str_replace(
                             Path::Separator,
                             "\\",
                             \str_replace(
                                 $Path,
                                 "",
                                 $FilesystemInfo->Directory->Path
                             )
                             . "\\" . $FilesystemInfo->Name
                         );
                try {
                    //Instantiate Test.
                    $Test = new $Class();
                    if(!$Test instanceof Test) {
                        $Tests[$Class] = [Test::Result => Test::Skipped];
                        continue;
                    }

                    //Run Test.
                    $Tests[$Class] = $Test->Run();

                    //Cleanup.
                    unset($Class);
                    \gc_collect_cycles();

                } catch(\Throwable $Exception) {
                    $Tests[$Class] = [
                        Test::Result  => Test::Crashed,
                        Test::Message => $Exception->getMessage(),
                        Test::File    => $Exception->getFile(),
                        Test::Line    => $Exception->getLine(),
                        Test::Trace   => $Exception->getTrace()
                    ];
                }
            }
        }

        return $Tests;

    }


}