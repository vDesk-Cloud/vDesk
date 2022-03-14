<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Environment\OS;
use vDesk\Struct\Collections\Collection;

/**
 * Provides methods for creating, moving, and enumerating through directories and subdirectories.
 *
 * @property-read string   $Path           Gets the path of the FileInfo.
 * @property-read string   $Directory      Gets the directory of the FileInfo.
 * @property \DateTime     $CreationTime   Gets or sets the creation-time of the underlying file of the DirectoryInfo.
 * @property-read bool     $Exists         Gets a value indicating whether the underlying directory of the DirectoryInfo exists.
 * @property string        $Extension      Gets or sets the extension of the underlying file of the DirectoryInfo.
 * @property string        $Name           Gets or sets the name of the underlying file of the FileInfo.
 * @property string        $FullName       Gets the full path-name of the underlying file of the DirectoryInfo.
 * @property \DateTime     $LastAccessTime Gets or sets the time the underlying file of the DirectoryInfo has been accessed for the last time.
 * @property \DateTime     $LastWriteTime  Gets or sets the time the underlying file of the DirectoryInfo has been modified for the last time.
 * @property-read int|null $Size           Gets the size of the underlying file of the DirectoryInfo.
 * @property null|string   $MimeType       Gets or sets the mime-type of the underlying file of the DirectoryInfo.
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @todo    Document Properties.
 */
final class DirectoryInfo extends FileSystemInfo {

    /**
     * Initializes a new instance of the DirectoryInfo class, which acts as a wrapper for a directory path.
     *
     * @param null|string $Path The fully qualified name of the directory, or the relative directory name.
     */
    public function __construct(protected ?string $Path = null) {
        $this->AddProperties([
            "Path"           => [
                \Get => fn(): string => $this->Path
            ],
            "Attributes"     => [
                \Get => fn() => null,
                \Set => fn($Value) => null
            ],
            "CreationTime"   => [
                \Get => fn(): \DateTime => $this->CreationTime ??= (new \DateTime())->setTimestamp(\filectime($this->Path)),
                \Set => function(\DateTime $Value) {
                    $this->CreationTime = $Value;
                    if($this->Exists) {
                        /** @todo Maybe implement this (ctime?) for unix? */
                        if(OS::Current === OS::NT) {
                            \shell_exec("powershell $(Get-Item {$this->Path}).creationtime = [datetime]'{$Value->format(\DateTime::ATOM)}'");
                        }
                    }
                }
            ],
            "Exists"         => [
                \Get => fn(): bool => $this->Exists ??= Directory::Exists($this->Path)
            ],
            "Extension"      => [
                \Get => fn() => null,
                \Set => fn(string $Value) => $this->Extension = $Value
            ],
            "Name"           => [
                \Get => fn(): ?string => $this->Name ??= Path::GetFileName($this->Path, true),
                \Set => function(string $Value): void {
                    $this->Name = $Value;
                    if($this->Exists) {
                        Directory::Rename($this->Path, $Value);
                    }
                }
            ],
            "FullName"       => [
                \Get => fn(): ?string => $this->FullName ??= Path::GetFullPath($this->Path) ?? $this->Path
            ],
            "LastAccessTime" => [
                \Get => fn(): \DateTime => (new \DateTime())->setTimestamp(\fileatime($this->Path)),
                \Set => fn(\DateTime $Value) => \touch($this->Path, \filemtime($this->Path), $Value->getTimestamp())
            ],
            "LastWriteTime"  => [
                \Get => fn(): \DateTime => (new \DateTime())->setTimestamp(\filemtime($this->Path)),
                \Set => fn(\DateTime $Value) => \touch($this->Path, $Value->getTimestamp())
            ],
            "Size"           => [
                \Get => fn(): ?int => $this->Size ??= Directory::Size($this->Path)
            ]
        ]);
    }

    /**
     * Creates a directory.
     *
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the target directoy does not exist.
     * @throws \vDesk\IO\IOException Thrown if the create operation failed.
     */
    public function Create(): void {
        if(!Directory::Exists($this->Path)) {
            // Check if the target directory exists.
            if(!Directory::Exists($TargetPath = Path::GetFileName($this->Path))) {
                throw new DirectoryNotFoundException("The directory at '{$TargetPath}' does not exist.");
            }
            // Check if the target already directory exists and if the creation failed..
            if(!Directory::Exists($this->Path) && !@\mkdir($this->Path)) {
                throw new IOException("Cannot create directory at '{$this->Path}'.");
            }
        }
    }

    /**
     * Deletes the underlying directory of the DirectoryInfo.
     *
     * @param bool $Children Determines whether to delete the specified directory, its subdirectories, and all files.
     *
     * @return bool True if the directory has been successfully deleted; otherwise, false.
     */
    public function Delete(bool $Children = false): bool {
        if(Directory::Exists($this->Path) && Directory::IsWritable($this->Path)) {
            Directory::Delete($this->Path, $Children);
            return true;
        }
        return false;
    }

    /**
     * Refreshes the state of the object.
     */
    public function Refresh(): void {
        \clearstatcache(true, $this->Path);
    }

    /**
     * Returns the names of files (including their paths) in the specified directory.
     *
     * @param string $Separator The path separator to use.
     *
     * @return \vDesk\Struct\Collections\Collection A Collection of FileInfos representing the files in the specified directory.
     */
    public function GetFiles(string $Separator = Path::Separator): Collection {
        return new Collection($this->IterateFiles($Separator));
    }

    /**
     * Returns the names of subdirectories (including their paths) in the specified directory.
     *
     * @param string $Separator The path separator to use.
     *
     * @return \vDesk\Struct\Collections\Collection A Collection of DirectoryInfos representing the directories in the specified directory.
     */
    public function GetDirectories(string $Separator = Path::Separator): Collection {
        return new Collection($this->IterateDirectories($Separator));
    }

    /**
     * Returns the names of all files and subdirectories in a specified path.
     *
     * @param string $Separator The path separator to use.
     *
     * @return \vDesk\Struct\Collections\Collection A Collection of FileSystemInfos representing the files and directories in the specified directory.
     */
    public function GetFileSystemEntries(string $Separator = Path::Separator): Collection {
        return new Collection($this->IterateFileSystemEntries($Separator));
    }

    /**
     * Returns a Generator that iterates over the names of files (including their paths) in the specified directory.
     *
     * @param string $Separator The path separator to use.
     *
     * @return \Generator A Generator that yields a FileInfo representing the files in the specified directory.
     */
    public function IterateFiles(string $Separator = Path::Separator): \Generator {
        $Stream = new DirectoryStream($this->Path);
        while(!$Stream->EndOfStream()) {
            if(!File::Exists($File = $this->Path . $Separator . $Stream->Read())) {
                continue;
            }
            yield new FileInfo($File);
        }
        $Stream->Close();
    }

    /**
     * Returns a Generator that iterates over the names of subdirectories (including their paths) in the specified directory.
     *
     * @param string $Separator The path separator to use.
     *
     * @return \Generator A Generator that yields a DirectoryInfo representing the directories in the specified directory.
     */
    public function IterateDirectories(string $Separator = Path::Separator): \Generator {
        $Stream = new DirectoryStream($this->Path);
        while(!$Stream->EndOfStream()) {
            if(!Directory::Exists($SubDirectory = $this->Path . $Separator . $Stream->Read())) {
                continue;
            }
            yield new DirectoryInfo($SubDirectory);
        }
        $Stream->Close();
    }

    /**
     * Returns a Generator that iterates over the names of all files and subdirectories in a specified path.
     *
     * @param string $Separator The path separator to use.
     *
     * @return \Generator A Generator that yields a FileSystemInfo representing the files and directories in the specified directory.
     */
    public function IterateFileSystemEntries(string $Separator = Path::Separator): \Generator {
        $Stream = new DirectoryStream($this->Path);
        while(!$Stream->EndOfStream()) {
            yield Directory::Exists($Entry = $this->Path . $Separator . $Stream->Read()) ? new DirectoryInfo($Entry) : new FileInfo($Entry);
        }
        $Stream->Close();
    }

}

