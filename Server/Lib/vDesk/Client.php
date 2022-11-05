<?php
declare(strict_types=1);

namespace vDesk;

use vDesk\IO\FileInfo;
use vDesk\IO\FileStream;
use vDesk\IO\Path;
use vDesk\IO\Stream\Mode;
use vDesk\Packages\Package;

/**
 * Class Client represents ...
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Client {
    
    /**
     * The stylesheets of the Client.
     *
     * @var string[]
     */
    public array $Design = [];
    
    /**
     * The library files of the Client.
     *
     * @var string[]
     */
    public array $Lib = [];
    
    /**
     * The Modules of the Client.
     *
     * @var string[]
     */
    public array $Modules = [];
    
    /**
     *
     */
    public function AddStylesheets() {
    
    }
    
    /**
     * Adds the client files of a specified Package to the Client.
     *
     * @param \vDesk\Packages\Package $Package The Package to add the files of.
     */
    public function AddPackage(Package $Package): void {
        foreach($Package::Resolve(Package::Client, Package::Design) as $Name => $FileSystemInfo) {
            if($FileSystemInfo instanceof FileInfo) {
                $this->Design[] = $Name;
            }
        }
        foreach($Package::Resolve(Package::Client, Package::Lib) as $Name => $FileSystemInfo) {
            if($FileSystemInfo instanceof FileInfo) {
                $this->Lib[] = $Name;
            }
        }
        foreach($Package::Resolve(Package::Client, Package::Modules) as $Name => $FileSystemInfo) {
            if($FileSystemInfo instanceof FileInfo) {
                $this->Modules[] = $Name;
            }
        }
    }
    
    /**
     * Creates a client html file at the specified path.
     *
     * @param string $Path The path of the client html file.
     */
    public function Create(string $Path): void {
        $File = new FileStream(($Path ?? \Client) . Path::Separator . "vDesk.html", Mode::Write | Mode::Truncate);
        $File->Write($this->ToHTML());
    }
    
    /**
     * Converts to files of the Client to a HTML representation.
     *
     * @param bool $Inline Flag indicating whether to compile the sources of the Client into a single html file.
     *
     * @return string The HTML representation of the Client.
     */
    public function ToHTML(bool $Inline = false): string {
        $Design  = \implode(\PHP_EOL,
            \array_map(static fn(string $Stylesheet): string => "        <link rel=\"stylesheet\" type=\"text/css\" href=\"./Design{$Stylesheet}\">", $this->Design));
        $Lib     = \implode(\PHP_EOL, \array_map(static fn(string $Script): string => "        <script src=\"./Lib{$Script}\"></script>", $this->Lib));
        $Modules = \implode(\PHP_EOL, \array_map(static fn(string $Module): string => "        <script src=\"./Modules{$Module}\"></script>", $this->Modules));
        
        return <<<CLIENT
<!DOCTYPE html>
<html>
    <head>
        <title>vDesk - virtual Desktop</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT"/>
        <meta http-equiv="pragma" content="no-cache"/>

        <!-- Design -->
{$Design}

        <!-- Lib -->
{$Lib}

        <!-- Modules -->
{$Modules}
    </head>
    <body class="vDesk" oncontextmenu="return false;" onload="vDesk.Start();">
    </body>
</html>
CLIENT;
    }
    
    /**
     *
     */
    public function Compose(): void {
    
    }
    
}