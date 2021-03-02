<?php
declare(strict_types=1);

namespace Modules;

use Pages\Reflect\Index;
use vDesk\Configuration\Settings;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\FileNotFoundException;
use vDesk\IO\Path;
use vDesk\IO\RecursiveFilesystemInfoIterator;

class Reflect extends \vDesk\Modules\Module {

    /**
     *
     */
    public const Namespace = "/(?:namespace (.+)[;|{]{1})/U";


    public static function Create(?string $Source = null, ?string $Target = null): void {

        $Source ??= \Server . Path::Separator . "Lib";
        $Target ??= \Server . Path::Separator . "Docu";


        \vDesk::$Load[] = static fn(string $Class): string => $Source . Path::Separator . \str_replace("\\", Path::Separator, $Class) . ".php";

        \vDesk::$Load[] = static fn(string $Class): string => Settings::$Local["Pages"]["Pages"]
                                                              . Path::Separator
                                                              . \str_replace("\\", Path::Separator, \str_replace("Pages", "", $Class))
                                                              . ".php";

        $Classes = [];
        /** @var FileInfo $FilesystemInfo */
        foreach(new RecursiveFilesystemInfoIterator(new DirectoryInfo($Source)) as $FilesystemInfo) {
            if($FilesystemInfo->Extension === "php") {
                $File = $FilesystemInfo->Open()->ReadAll();
                \preg_match("/(?:namespace (.+)[;|{]{1})/U", $File, $Matches);

                // Use only files containing either classes, interfaces or traits as of usage of Reflection.
                if((bool)\preg_match("/(?:[^.#]*(class|interface|trait))/", $File)) {
                    $Classes[] = ($Matches[1] ?? "") . "\\" . $FilesystemInfo->Name;
                    include_once $FilesystemInfo->FullName;
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

            }

        }
        \ob_end_clean();
        foreach($Reflectors as $Reflector) {

            try {

                $Page = new \Pages\Reflect(
                    Reflector: $Reflector,
                    Index: new Index(Reflectors: $Reflectors)
                );

                echo "Creating documentation for {$Reflector->name}<br>";

                $File = File::Create($Target . Path::Separator . $Page->ReferenceName . ".html", true);
                $File->Write((string)$Page);
                $File->Close();
                unset($Page);

            }catch(\Throwable $Exception) {
                echo "Couldn't create documentation for {$Reflector->name}: {$Exception->getMessage()}<br>";
            }

        }

        echo "Done!";
    }
}