<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Data\IDataView;
use vDesk\IO\Directory;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\IO\RecursiveFilesystemInfoIterator;
use vDesk\Struct\Text;

/**
 * Abstract baseclass for installable Packages.
 *
 * @package vDesk\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Package implements IDataView {
    
    /**
     * The client target directory name of the Package.
     */
    public const Client = "Client";
    
    /**
     * The server target directory name of the Package.
     */
    public const Server = "Server";
    
    /**
     * The client stylesheet directory name of the Package.
     */
    public const Design = "Design";
    
    /**
     * The library directory name of the Package.
     */
    public const Lib = "Lib";
    
    /**
     * The Modules directory name of the Package.
     */
    public const Modules = "Modules";
    
    /**
     * The name of the Package.
     */
    public const Name = "";
    
    /**
     * The version of the Package.
     */
    public const Version = "0.0.0";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "";
    
    /**
     * The name of the Package.
     */
    public const Description = "";
    
    /**
     * The license of the Package.
     */
    public const License = "Ms-PL";
    
    /**
     * The license text of the Package.
     */
    public const LicenseText = "See about dialog for license text";
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [],
            self::Modules => [],
            self::Lib     => []
        ],
        self::Server => [
            self::Modules => [],
            self::Lib     => []
        ]
    ];
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [];
    
    /**
     * Composes the files and folders of the Package into a specified Phar archive.
     *
     * @param \Phar $Phar The target Phar archive.
     */
    final public static function Compose(\Phar $Phar): void {
        //Bundle files.
        foreach(static::Files as $Target => $Types) {
            foreach($Types as $Type => $Resources) {
                if(\is_array($Resources)) {
                    $Phar->addEmptyDir("/{$Target}/{$Type}");
                    foreach(static::Resolve($Target, $Type) as $Name => $FileSystemInfo) {
                        if($FileSystemInfo instanceof FileInfo) {
                            $Phar->addFile($FileSystemInfo->FullName, "/{$Target}/{$Type}{$Name}");
                        } else if($FileSystemInfo instanceof DirectoryInfo) {
                            $Phar->addEmptyDir("/{$Target}/{$Type}{$Name}");
                        }
                    }
                } else {
                    foreach(static::Resolve($Target, $Resources) as $Name => $FileSystemInfo) {
                        if($FileSystemInfo instanceof FileInfo) {
                            $Phar->addFile($FileSystemInfo->FullName, "/{$Target}{$Name}");
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Deploys the files and folders of the Phar archive of the Package to a specified path.
     *
     * @param \Phar  $Phar The Phar archive to extract.
     * @param string $Path The path to extract the files and folders into.
     */
    final public static function Deploy(\Phar $Phar, string $Path): void {
        //Extract files.
        foreach(static::Files as $Target => $Types) {
            foreach($Types as $Type => $Resources) {
                if(\is_array($Resources)) {
                    foreach(static::Resolve($Target, $Type, $Phar) as $Name => $Entry) {
                        if($Entry instanceof FileInfo) {
                            File::Copy($Entry->FullName, $Path . Path::Separator . $Target . Path::Separator . $Type . $Name);
                        } else {
                            Directory::Create($Path . Path::Separator . $Target . Path::Separator . $Type . $Name);
                        }
                    }
                } else {
                    foreach(static::Resolve($Target, $Resources, $Phar) as $Name => $Entry) {
                        if($Entry instanceof FileInfo) {
                            File::Copy($Entry->FullName, $Path . Path::Separator . $Target . Path::Separator . $Name);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Undeploys the files and folders of the Phar archive of the Package from a specified path.
     */
    final public static function Undeploy(): void {
        //Extract files.
        foreach(static::Files as $Target => $Types) {
            foreach($Types as $Type => $Resources) {
                if(\is_array($Resources)) {
                    foreach(static::Resolve($Target, $Type) as $Name => $Entry) {
                        $Entry->Delete(true);
                    }
                } else {
                    foreach(static::Resolve($Target, $Resources) as $Name => $Entry) {
                        $Entry->Delete(true);
                    }
                }
            }
        }
    }
    
    /**
     * Resolves the files and folders of a specified target and type of the Package.
     *
     * @param string $Target The target of the files to resolve.
     * @param string $Type   The type of the files to resolve.
     * @param \Phar  $Phar   The Phar archive of the Package.
     *
     * @return \Generator A Generator that yields the files and folders of the Package.
     */
    public static function Resolve(string $Target, string $Type, \Phar $Phar = null): \Generator {
        if(isset(static::Files[$Target][$Type])) {
            if($Phar !== null) {
                $Root = "phar://{$Phar->getPath()}/{$Target}/{$Type}";
                foreach(static::Files[$Target][$Type] ?? [] as $Path) {
                    
                    //Check if the path starts with a separator.
                    if(!Text::StartsWith($Path, "/") && !Text::StartsWith($Path, Path::Separator)) {
                        $Path = "/{$Path}";
                    }
                    
                    //Create Package path.
                    $Name = (string)Text::Replace($Path, "/", Path::Separator);
                    
                    //Normalize path.
                    $Filepath = $Root . Text::Replace($Path, Path::Separator, "/");
                    
                    if(File::Exists($Filepath)) {
                        yield $Name => new FileInfo($Filepath);
                    } else if(Directory::Exists($Filepath)) {
                        $Directory = new DirectoryInfo($Filepath);
                        yield $Name => $Directory;
                        
                        //Resolve contents of the specified folder.
                        foreach(new RecursiveFilesystemInfoIterator($Directory, true, "/") as $FileSystemInfo) {
                            $Name = Text::Replace($FileSystemInfo->FullName, $Root, "");
                            yield (string)$Name->Substring(Text::LastIndexOf((string)$Name, $Path))->Replace("/", Path::Separator)
                            =>
                            $FileSystemInfo;
                        }
                    }
                }
                
            } else {
                $Root = ($Target === self::Client ? \Client : \Server) . Path::Separator . $Type;
                foreach(static::Files[$Target][$Type] ?? [] as $Path) {
                    
                    //Check if the path starts with a separator.
                    if(!Text::StartsWith($Path, "/") && !Text::StartsWith($Path, Path::Separator)) {
                        $Path = "/" . $Path;
                    }
                    
                    //Create Package path.
                    $Name = (string)Text::Replace($Path, $Root, "")
                                        ->Replace(Path::Separator, "/");
                    
                    //Normalize path.
                    $Filepath = $Root . Text::Replace($Path, "/", Path::Separator);
                    
                    if(File::Exists($Filepath)) {
                        yield $Name => new FileInfo($Filepath);
                    } else if(Directory::Exists($Filepath)) {
                        $Directory = new DirectoryInfo($Filepath);
                        yield $Name => $Directory;
                        
                        //Resolve contents of the specified folder.
                        foreach(new RecursiveFilesystemInfoIterator($Directory, true) as $FileSystemInfo) {
                            $Name = Text::Replace($FileSystemInfo->FullName, Path::Separator, "/");
                            yield (string)$Name->Substring(Text::LastIndexOf((string)$Name, $Path))
                            =>
                            $FileSystemInfo;
                        }
                    }
                }
            }
        } else if($Phar !== null) {
            $Name     = Text::Replace($Type, "/", "")->Replace(Path::Separator, "");
            $Filepath = "phar://{$Phar->getPath()}/{$Target}/{$Name}";
            if(File::Exists($Filepath)) {
                yield Path::Separator . $Name => new FileInfo($Filepath);
            }
        } else {
            $Name     = Text::Replace($Type, "/", "")->Replace(Path::Separator, "");
            $Filepath = ($Target === self::Client ? \Client : \Server) . Path::Separator . $Name;
            if(File::Exists($Filepath)) {
                yield "/{$Name}" => new FileInfo($Filepath);
            }
            
        }
        
    }
    
    /**
     * Pre-installs the Package to the specified path.
     *
     * @param \Phar  $Phar The Phar archive of the Package.
     * @param string $Path The installation path of the Package.
     */
    public static function PreInstall(\Phar $Phar, string $Path): void {
    
    }
    
    /**
     * Installs the Package to the specified path.
     *
     * @param \Phar  $Phar The Phar archive of the Package.
     * @param string $Path The installation path of the Package.
     *
     * @todo Evaluate return boolean flag indicating whether the installation of the Package has been successful to perform rollbacks in error cases.
     */
    abstract public static function Install(\Phar $Phar, string $Path): void;
    
    /**
     * Post-installs the Package to the specified path.
     *
     * @param \Phar  $Phar The Phar archive of the Package.
     * @param string $Path The installation path of the Package.
     */
    public static function PostInstall(\Phar $Phar, string $Path): void {
    
    }
    
    /**
     * Uninstalls the Package from the specified path.
     *
     * @param string $Path The installation path of the Package.
     */
    abstract public static function Uninstall(string $Path): void;
    
    /**
     * Creates a data view of the Package.
     *
     * @return array The data view representing the current state of the Package.
     */
    final public function ToDataView(): array {
        return [
            "Name"         => static::Name,
            "Version"      => static::Version,
            "Dependencies" => static::Dependencies,
            "Vendor"       => static::Vendor,
            "Description"  => static::Description,
            "License"      => static::License,
            "LicenseText"  => static::LicenseText
        ];
    }
    
    /**
     * @inheritDoc
     */
    final public static function FromDataView(mixed $DataView): IDataView {
        throw new \RuntimeException(__METHOD__ . " is not supported!");
    }
    
}
