<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Crash\Attribute;
use vDesk\Crash\Test;
use vDesk\IO\Directory;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\IO\RecursiveFilesystemInfoIterator;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Struct\Type;

/**
 * Crash\Test Module class.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Crash extends Module {

    /**
     * The path containing the installed Crash\Tests.
     */
    public const Tests = \Server
                         . Path::Separator . "Lib"
                         . Path::Separator . "vDesk"
                         . Path::Separator . "Crash"
                         . Path::Separator . "Tests";

    /**
     * Loads and runs the Tests from a specified path.
     *
     * @param null|string $Path The path to load the Test from.
     *
     * @return array An array containing information about the results if the executed Tests.
     */
    public static function Test(?string $Path = null): array {
        $Path ??= Command::$Parameters["Path"] ?? self::Tests;

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
                    $Tests[$Class] = $Test();

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

    /**
     * Creates a Test class from a specified class.
     *
     * @param null|string $Class     The fully qualified class name of the class to create a Test from.
     * @param bool        $Inherit   Flag indicating whether to include inherited methods as Test cases.
     * @param bool        $Overwrite Flag indicating whether to overwrite any existing Test class.
     *
     * @return string|null The fully qualified name of the created Test class; otherwise, null.
     *
     * @throws \InvalidArgumentException Thrown if the specified class doesn't exist.
     */
    public static function Create(?string $Class = null, bool $Inherit = false, bool $Overwrite = false): ?string {
        $Class ??= Command::$Parameters["Class"];
        if(!\class_exists($Class)) {
            throw new \InvalidArgumentException("Class \"{$Class}\" doesn't exist!");
        }
        $Reflector = new \ReflectionClass($Class);
        if($Reflector->isAbstract()){
            return null;
        }

        //Create target directory.
        $Directory = \str_replace("\\", Path::Separator, $Reflector->getNamespaceName());
        if(!Directory::Exists(self::Tests . Path::Separator . $Directory)) {
            $Path = self::Tests;
            foreach(\explode("\\", $Directory) as $Namespace) {
                $Path .= Path::Separator . $Namespace;
                if(!Directory::Exists($Path)) {
                    Directory::Create($Path);
                }
            }
        }
        $Path = self::Tests . Path::Separator . $Directory . Path::Separator . $Reflector->getShortName() . ".php";
        if(!$Overwrite && File::Exists($Path)) {
            return null;
        }

        //Create Test file.
        $File = File::Create($Path, true);

        //Create Test.
        $File->Write("<?php" . \PHP_EOL);
        $File->Write("declare(strict_types=1);" . \PHP_EOL . \PHP_EOL);
        $File->Write("namespace vDesk\\Crash\\Tests\\{$Reflector->getNamespaceName()};" . \PHP_EOL . \PHP_EOL);
        $File->Write("use vDesk\\Crash\\Test;" . \PHP_EOL . \PHP_EOL);
        $File->Write("class {$Reflector->getShortName()} extends Test {" . \PHP_EOL . \PHP_EOL);

        //Create constructor.
        $File->Write("    public function __construct(protected ?\\{$Reflector->getName()} \${$Reflector->getShortName()} = null) {" . \PHP_EOL);
        $File->Write("        \$this->{$Reflector->getShortName()} = new \\{$Reflector->getName()}();" . \PHP_EOL);
        $File->Write("    }" . \PHP_EOL . \PHP_EOL);

        //Create Test cases.
        foreach($Reflector->getMethods() as $Method) {
            //Skip magic and internal methods.
            if(
                $Method->isConstructor()
                || $Method->isDestructor()
                || $Method->isAbstract()
                || $Method->isInternal()
                || $Method->isPrivate()
                || \str_starts_with($Method->getName(), "__")
                || (!$Inherit && $Method->getDeclaringClass()->getName() !== $Reflector->getName())
            ) {
                continue;
            }

            //Copy Attributes.
            $Attributes = \array_merge(
                $Method->getAttributes(Test\Case\Crash::class),
                $Method->getAttributes(Test\Case\Repeat::class),
                $Method->getAttributes(Test\Case\Penetrate::class),
                $Method->getAttributes(Test\Case\Skip::class)
            );
            if(\count($Attributes) > 0) {
                foreach($Attributes as $Attribute) {
                    $File->Write("    " . Attribute::FromDataView($Attribute) . \PHP_EOL);
                }
            }

            //Create Test case stub.
            $File->Write("    public function {$Method->getName()}(): void {" . \PHP_EOL);

            //Format parameters.
            $Parameters = [];
            foreach($Method->getParameters() as $Parameter) {
                $Parameters[] = "{$Parameter->getName()}: " . match ($Parameter->isDefaultValueAvailable()) {
                        true => \json_encode($Parameter->getDefaultValue()),
                        false => match ($Type = \ltrim((string)$Parameter->getType(), "?")) {
                            Type::Int => \random_int(-1000000, 1000000),
                            Type::Float => \random_int(-1000000, 1000000) / \random_int(1, 9),
                            Type::String => \json_encode(["Lorem", "Ipsum", "Dolor", "Sit", "Amet"][\random_int(0, 4)]),
                            Type::Bool, Type::Boolean => \json_encode((bool)\random_int(0, 1)),
                            Type::Array => \json_encode(\range(\random_int(0, 10), \random_int(100, 1000), \random_int(1, 10))),
                            Type::Callable => "static fn() => \"Replace me\"",
                            Type::Mixed, "" => "null",
                            default => "new \\{$Type}()"
                        }
                    };
            }

            //Create default assertion.
            $File->Write(
                "        \assert(\$this->{$Reflector->getShortName()}"
                . ($Method->isStatic() ? "::" : "->")
                . "{$Method->getName()}("
                . \implode(", ", $Parameters)
            );
            $File->Write(") ");

            //Format return type.
            if($Method->hasReturnType()) {
                $File->Write(
                    match ($Type = \ltrim((string)$Method->getReturnType(), "?")) {
                        Type::Int => "=== " . \random_int(-1000000, 1000000),
                        Type::Float => "=== " . \random_int(-1000000, 1000000) / \random_int(1, 9),
                        Type::String => "=== " . \json_encode(["Lorem", "Ipsum", "Dolor", "Sit", "Amet"][\random_int(0, 4)]),
                        Type::Bool, Type::Boolean => "=== " . \json_encode((bool)\random_int(0, 1)),
                        Type::Array => "=== " . \json_encode(\range(\random_int(0, 10), \random_int(100, 1000), \random_int(1, 10))),
                        Type::Mixed, "void" => "=== null",
                        "self" => "instanceof \\{$Reflector->getName()}",
                       // "self" => "instanceof \\". \ltrim((string)$Method->getReturnType(), "?"),
                        default => "instanceof \\{$Type}"
                    }
                );
            }
            $File->Write(");" . \PHP_EOL);
            $File->Write("    }" . \PHP_EOL . \PHP_EOL);
        }

        $File->Write("}" . \PHP_EOL);
        $File->Close();

        return "vDesk\\Crash\\Tests\\{$Class}";

    }

    /**
     * Creates a set of Test classes from a specified path.
     *
     * @param null|string $Path      The path to iterate through.
     * @param bool        $Inherit   Flag indicating whether to include inherited methods as Test cases.
     * @param bool        $Overwrite Flag indicating whether to overwrite any existing Test class.
     *
     * @return array An array containing the created Test classes.
     */
    public static function CreateFromPath(?string $Path = null, bool $Inherit = false, bool $Overwrite = false): array {
        $Path  ??= Command::$Parameters["Path"];
        $Tests = [];
        /** @var FileInfo $FilesystemInfo */
        foreach(new RecursiveFilesystemInfoIterator(new DirectoryInfo($Path)) as $FilesystemInfo) {
            if($FilesystemInfo->Extension === "php") {
                $Class = \str_replace(
                    Path::Separator,
                    "\\",
                    \str_replace(
                        \Server . Path::Separator . "Lib" . Path::Separator,
                        "",
                        $FilesystemInfo->Directory->Path
                    )
                    . "\\" . $FilesystemInfo->Name
                );
                if(\class_exists($Class)) {
                    $Test = self::Create($Class, $Inherit, $Overwrite);
                    if($Test !== null){
                        $Tests[] = $Test;
                    }
                }

            }
        }

        return $Tests;
    }


}