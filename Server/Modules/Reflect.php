<?php
declare(strict_types=1);

namespace Modules;

use Pages\Reflect\Index;
use Pages\Reflect\Summary;
use vDesk\Configuration\Settings;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\IO\RecursiveFilesystemInfoIterator;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Pages\IPackage;

class Reflect extends Module {

    /**
     * Expression for parsing namespace declarations from PHP files.
     */
    public const Namespace = "/(?:namespace (.+)[;|{]{1})/U";

    /**
     * Expression for parsing class-, interface- and trait declarations from PHP files.
     */
    public const Documentation = "/(?:[^.#]*(class|interface|trait))/";

    /**
     * Creates an API documentation
     *
     * @param null|string $Source The path to the
     * @param null|string $Target
     */
    public static function Create(?string $Source = null, ?string $Target = null): void {
        $Source ??= Command::$Parameters["Source"] ?? \Server . Path::Separator . "Lib";
        $Target ??= Command::$Parameters["Target"] ?? \Server . Path::Separator . "Reflect";

        \vDesk::$Load[] = static fn(string $Class): string => $Source . Path::Separator . \str_replace("\\", Path::Separator, $Class) . ".php";

        $Errors     = [];
        $Exceptions = [];
        \set_error_handler(
            static function($Code, $Message, $File, $Line, $Context = []) use (&$Errors) {
                $Errors[] = "[{$Code}]{$Message} in file: {$File} on line: {$Line}" . \print_r($Context, true);
            }
        );
        \set_exception_handler(
            static function(\Throwable $Exception) use (&$Exceptions) {
                $Exceptions[] = $Exception;
            }
        );

        //Search for class files.
        $Classes = [];
        /** @var FileInfo $FilesystemInfo */
        foreach(new RecursiveFilesystemInfoIterator(new DirectoryInfo($Source)) as $FilesystemInfo) {
            if($FilesystemInfo->Extension === "php") {
                $File = $FilesystemInfo->Open()->ReadAll();
                \preg_match(self::Namespace, $File, $Matches);

                //Use only files containing either classes, interfaces or traits as of usage of Reflection.
                if((bool)\preg_match(self::Documentation, $File)) {
                    $Classes[] = ($Matches[1] ?? "") . "\\" . $FilesystemInfo->Name;
                    try {
                        include_once $FilesystemInfo->FullName;
                    } catch(\Throwable $Exception) {
                        $Exceptions[] = $Exception;
                    }
                }
            }
        }

        $Reflectors = [];
        //Sort out internal classes.
        foreach($Classes as $Class) {
            try {
                $Reflector = new \ReflectionClass($Class);
                if($Reflector->isInternal()) {
                    continue;
                }
                $Reflectors[] = $Reflector;
            } catch(\Throwable $Exception) {
                $Exceptions[] = $Exception;
            }
        }
        \usort($Reflectors, static fn(\ReflectionClass $First, \ReflectionClass $Second): int => \strnatcmp($First->name, $Second->name));

        //Create documentation.
        foreach($Reflectors as $Reflector) {
            try {
                $Page = new \Pages\Reflect(
                    Reflector: $Reflector,
                    Index: new Index(Reflectors: $Reflectors)
                );
                $File = File::Create($Target . Path::Separator . $Page->ReferenceName . ".html", true);
                $File->Write((string)$Page);
                $File->Close();
            } catch(\Throwable $Exception) {
                $Exceptions[] = $Exception;
            }
        }

        //Create summary.
        $Page = new Summary(
            Reflectors: $Reflectors,
            Index: new Index(Reflectors: $Reflectors),
            Errors: $Errors,
            Exceptions: $Exceptions
        );
        $File = File::Create($Target . Path::Separator . "index.html", true);
        $File->Write((string)$Page);
        $File->Close();

        //Copy stylesheet.
        $File = File::Create($Target . Path::Separator . "Stylesheet.css", true);
        $File->Write(
            File::OpenRead(\Server . Path::Separator . IPackage::Stylesheets . Path::Separator . \vDesk\Packages\Reflect::Name . Path::Separator . "Stylesheet.css")
                ->ReadAll()
        );
        $File->Close();

        //Copy Search.js.
        $File = File::Create($Target . Path::Separator . "Search.js", true);
        $File->Write(
            File::OpenRead(\Server . Path::Separator . IPackage::Scripts . Path::Separator . \vDesk\Packages\Reflect::Name . Path::Separator . "Search.js")
                ->ReadAll()
        );
        $File->Close();

        echo "Generated documentation for " . \count($Reflectors) . " files.";
    }
}