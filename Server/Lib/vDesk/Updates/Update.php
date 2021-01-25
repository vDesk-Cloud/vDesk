<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Data\IDataView;
use vDesk\IO\Directory;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\IO\RecursiveFilesystemInfoIterator;
use vDesk\Packages\Package;
use vDesk\Struct\Text;

/**
 * Abstract baseclass for installable Updates.
 *
 * @package vDesk\Updates
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Update implements IDataView {
    
    /**
     * The class name of the Package of the Update.
     */
    public const Package = Package::class;
    
    /**
     * The min Package version of the Update.
     */
    public const RequiredVersion = "0.0.0";
    
    /**
     * The description of the Update.
     */
    public const Description = "";
    
    /**
     * The files and directories to deploy of the Update.
     */
    public const Deploy = "Deploy";
    
    /**
     * The files and directories to undeploy of the Update.
     */
    public const Undeploy = "Undeploy";
    
    /**
     * The source of local hosted Updates.
     */
    public const Hosted = "Hosted";
    
    /**
     * The MIME-Type of the Update.
     */
    public const MimeType = "vdesk/update";
    
    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Design  => [],
                Package::Modules => [],
                Package::Lib     => []
            ],
            Package::Server => [
                Package::Modules => [],
                Package::Lib     => []
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Design  => [],
                Package::Modules => [],
                Package::Lib     => []
            ],
            Package::Server => [
                Package::Modules => [],
                Package::Lib     => []
            ]
        ]
    ];
    
    /**
     * Composes the files and folders of the Update into a specified Phar archive.
     *
     * @param \Phar $Phar The target Phar archive.
     */
    final public static function Compose(\Phar $Phar): void {
        //Bundle files.
        foreach(static::Files[self::Deploy] as $Target => $Types) {
            foreach($Types as $Type => $Resources) {
                if(\is_array($Resources)) {
                    $Phar->addEmptyDir("/{$Target}/{$Type}");
                    foreach(self::Resolve(self::Deploy, $Target, $Type) as $Name => $FileSystemInfo) {
                        if($FileSystemInfo instanceof FileInfo) {
                            $Phar->addFile($FileSystemInfo->FullName, "/{$Target}/{$Type}{$Name}");
                        } else if($FileSystemInfo instanceof DirectoryInfo) {
                            $Phar->addEmptyDir("/{$Target}/{$Type}{$Name}");
                        }
                    }
                } else {
                    foreach(self::Resolve(self::Deploy, $Target, $Resources) as $Name => $FileSystemInfo) {
                        if($FileSystemInfo instanceof FileInfo) {
                            $Phar->addFile($FileSystemInfo->FullName, "/{$Target}{$Name}");
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Deploys the files and folders of the Phar archive of the Update to a specified path.
     *
     * @param \Phar                   $Phar    The Phar archive to extract.
     * @param string                  $Path    The path to extract the files and folders into.
     */
    final public static function Deploy(\Phar $Phar, string $Path): void {
        //Extract files.
        foreach(static::Files[self::Deploy] as $Target => $Types) {
            foreach($Types as $Type => $Resources) {
                if(\is_array($Resources)) {
                    foreach(static::Resolve(self::Deploy, $Target, $Type, $Phar) as $Name => $Entry) {
                        $FullPath = $Path . Path::Separator . $Target . Path::Separator . $Type . $Name;
                        if($Entry instanceof FileInfo) {
                            if(File::Exists($FullPath)) {
                                File::Delete($FullPath);
                            }
                            File::Copy($Entry->FullName, $FullPath);
                        } else {
                            if(Directory::Exists($FullPath)) {
                                continue;
                            }
                            Directory::Create($FullPath);
                        }
                    }
                } else {
                    foreach(static::Resolve(self::Deploy, $Target, $Resources, $Phar) as $Name => $Entry) {
                        if($Entry instanceof FileInfo) {
                            $FullPath = $Path . Path::Separator . $Target . Path::Separator . $Name;
                            if(File::Exists($FullPath)) {
                                File::Delete($FullPath);
                            }
                            File::Copy($Entry->FullName, $FullPath);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Undeploys the files and folders of the Phar archive of the Update from a specified path.
     */
    final public static function Undeploy(): void {
        //Remove files.
        foreach(static::Files[self::Undeploy] as $Target => $Types) {
            foreach($Types as $Type => $Resources) {
                if(\is_array($Resources)) {
                    foreach(static::Resolve(self::Undeploy, $Target, $Type) as $Name => $Entry) {
                        $Entry->Delete(true);
                    }
                } else {
                    foreach(static::Resolve(self::Undeploy, $Target, $Resources) as $Name => $Entry) {
                        $Entry->Delete(true);
                    }
                }
            }
        }
    }
    
    /**
     * Resolves the files and folders of a specified target and type of the Package.
     *
     * @todo Blame developer for copy-coding..  ..oh wait, I'm the developer..
     * @todo Consider moving logic to a separate trait.
     *
     * @param string     $Deployment The deployment type of the files to resolve.
     * @param string     $Target     The target of the files to resolve.
     * @param string     $Type       The type of the files to resolve.
     * @param null|\Phar $Phar       The Phar archive of the Update.
     *
     * @return \Generator A Generator that yields the files and folders of the Package.
     */
    public static function Resolve(string $Deployment, string $Target, string $Type, \Phar $Phar = null): \Generator {
        if(isset(static::Files[$Deployment][$Target][$Type])) {
            if($Phar !== null) {
                $Root = "phar://{$Phar->getPath()}/{$Target}/{$Type}";
                foreach(static::Files[self::Deploy][$Target][$Type] ?? [] as $Path) {
                    
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
                $Root = ($Target === Package::Client ? \Client : \Server) . Path::Separator . $Type;
                foreach(static::Files[$Deployment][$Target][$Type] ?? [] as $Path) {
                    
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
            $Filepath = ($Target === Package::Client ? \Client : \Server) . Path::Separator . $Name;
            if(File::Exists($Filepath)) {
                yield "/{$Name}" => new FileInfo($Filepath);
            }
            
        }
        
    }
    
    /**
     * Installs the Update to the specified path.
     *
     * @param \Phar  $Phar The Phar archive of the Update.
     * @param string $Path The installation path of the Update.
     */
    abstract public static function Install(\Phar $Phar, string $Path): void;
    
    /**
     * Creates a data view of the Update.
     *
     * @return array The data view representing the current state of the Update.
     */
    final public function ToDataView(): array {
        return [
            "Package"         => static::Package::Name,
            "Version"         => static::Package::Version,
            "RequiredVersion" => static::RequiredVersion,
            "Dependencies"    => static::Package::Dependencies,
            "Vendor"          => static::Package::Vendor,
            "Description"     => static::Description
        ];
    }
    
    /**
     * @inheritDoc
     */
    final public static function FromDataView(mixed $DataView): IDataView {
        throw new \RuntimeException(__METHOD__ . " is not supported!");
    }
}