<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Environment\OS;
use vDesk\IO\Stream\Mode;

/**
 * Provides properties and methods for the creation, copying, deletion, moving, and opening of files, and aids in the creation of
 * FileStream objects.
 *
 * @property-read string                  $Path           Gets the path of the FileInfo.
 * @property-read \vDesk\IO\DirectoryInfo $Directory      Gets the parent directory of the FileInfo.
 * @property \DateTime                    $CreationTime   Gets or sets the creation-time of the underlying file of the FileInfo.
 * @property-read bool                    $Exists         Gets a value indicating whether the underlying file of the FileInfo exists.
 * @property string                       $Extension      Gets or sets the extension of the underlying file of the FileInfo.
 * @property string                       $Name           Gets or sets the name of the underlying file of the FileInfo.
 * @property string                       $FullName       Gets the full path-name of the underlying file of the FileInfo.
 * @property \DateTime                    $LastAccessTime Gets or sets the time the underlying file of the FileInfo has been accessed for the last time.
 * @property \DateTime                    $LastWriteTime  Gets or sets the time the underlying file of the FileInfo has been modified for the last time.
 * @property-read int|null                $Size           Gets the size of the underlying file of the FileInfo.
 * @property null|string                  $MimeType       Gets or sets the mime-type of the underlying file of the FileInfo.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
final class FileInfo extends FileSystemInfo {
    
    /**
     * The mime-type of the underlying file of the FileInfo.
     *
     * @var null|string
     */
    private ?string $MimeType;
    
    /**
     * Initializes a new instance of the FileInfo class, which acts as a wrapper for a file path.
     *
     * @param string $Path The fully qualified name of the new file, or the relative file name.
     */
    public function __construct(string $Path) {
        $this->Path = $Path;
        $this->AddProperties([
            "Path"           => [
                \Get => fn(): string => $this->Path
            ],
            "Directory"      => [
                \Get => fn(): DirectoryInfo => $this->Directory ??= new DirectoryInfo(Path::GetPath($this->Path))
            ],
            "CreationTime"   => [
                \Get => fn(): \DateTime => $this->CreationTime ??= OS::Current === OS::MacOS
                    ? \DateTime::createFromFormat("m\\d\\Y H:i:s", \shell_exec("$ GetFileInfo -d {$this->Path}"))
                    : (new \DateTime())->setTimestamp(\filectime($this->Path)),
                \Set => function(\DateTime $Value): void {
                    $this->CreationTime = $Value;
                    if($this->Exists) {
                        switch(OS::Current) {
                            case OS::NT:
                                \shell_exec("powershell $(Get-Item {$this->Path}).creationtime = [datetime]'{$Value->format(\DateTime::ATOM)}'");
                                break;
                            case OS::MacOS:
                                \shell_exec("SetFile -d '{$Value->format("m\\d\\Y H:i:s")}' {$this->Path}");
                                break;
                        }
                    }
                }
            ],
            "Exists"         => [
                \Get => fn(): bool => $this->Exists ??= File::Exists($this->Path)
            ],
            "Extension"      => [
                \Get => fn(): ?string => $this->Extension ??= Path::GetExtension($this->Path),
                \Set => function(string $Value): void {
                    $this->Extension = $Value;
                    if($this->Exists) {
                        File::Rename($this->Path, Path::ChangeExtension($this->Path, $Value));
                    }
                }
            ],
            "Name"           => [
                \Get => fn(): ?string => $this->Name ??= Path::GetFileName($this->Path, false),
                \Set => function(string $Value): void {
                    $this->Name = $Value;
                    if($this->Exists) {
                        File::Rename($this->Path, "{$Value}." . $this->Extension ??= Path::GetExtension($this->Path));
                    }
                }
            ],
            "FullName"       => [
                \Get => fn(): ?string => $this->FullName ??= Path::GetFullPath($this->Path) ?? $this->Path,
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
                \Get => fn(): ?int => $this->Size ??= File::Size($this->Path)
            ],
            "MimeType"       => [
                \Get => fn(): ?string => $this->MimeType,
                \Set => fn(?string $Value) => $this->MimeType = $Value
            ]
        ]);
    }
    
    /**
     * Creates or overwrites a file in the specified path of the FileInfo.
     *
     * @return FileStream A FileStream that provides read/write access to the file specified in $Path.
     * @throws \vDesk\IO\IOException Thrown if a file at the specified path already exists.
     *
     */
    public function Create(): FileStream {
        if(File::Exists($this->Path)) {
            throw new IOException("File {$this->Path} already exists.");
        }
        return File::Create($this->Path);
    }
    
    /**
     * Deletes the underlying file of the FileInfo.
     *
     * @return bool True if the file has been successfully deleted; otherwise, false.
     */
    public function Delete(): bool {
        if(File::Exists($this->Path) && File::IsWritable($this->Path)) {
            File::Delete($this->Path);
            $this->Exists = false;
            return true;
        }
        return false;
    }
    
    /**
     * Refreshes the state of the file of the FileInfo in the underlying filesystem-cache.
     */
    public function Refresh(): void {
        \clearstatcache(true, $this->Path);
        $this->Directory = null;
        $this->FullName  = null;
        $this->Size  = null;
        $this->Exists  = null;
    }
    
    /**
     * Moves the file of the FileInfo to a new destination.
     *
     * @param string $Destination The path of the new destination.
     * @param string $Separator   The path separator to use.
     *
     * @return bool True if the file has been successfully moved; otherwise, false.
     */
    public function Move(string $Destination, string $Separator = Path::Separator): bool {
        if(File::Exists($this->Path) && File::IsWritable($this->Path)) {
            File::Move($this->Path, $Destination);
            $this->Directory = null;
            $this->FullName  = null;
            $this->Path      = $Destination . $Separator . Path::GetFileName();
            return true;
        }
        return false;
    }
    
    /**
     * Renames the file of the FileInfo to a specified new name.
     *
     * @param string $Name      The new name of the specified file.
     * @param string $Separator The path separator to use.
     *
     * @return bool True if the file has been successfully renamed; otherwise, false.
     */
    public function Rename(string $Name, string $Separator = Path::Separator): bool {
        if(File::Exists($this->Path) && File::IsWritable($this->Path)) {
            File::Rename($this->Path, "{$Name}." . $this->Extension ??= Path::GetExtension($this->Path));
            $this->Name     = null;
            $this->FullName = null;
            $this->Path     = $this->Directory->Path . $Separator . $Name;
            return true;
        }
        return false;
    }
    
    /**
     * Copies the file of the FileInfo to a new file.
     *
     * @param string $Destination The path of the new destination.
     *
     * @return bool True if the file has been successfully copied; otherwise, false.
     */
    public function Copy(string $Destination): bool {
        if(File::Exists($this->Path) && File::IsReadable($this->Path)) {
            File::Copy($this->Path, $Destination);
            return true;
        }
        return false;
    }
    
    /**
     * Opens a FileStream on the specified path of the FileInfo with read/write access.
     *
     * @param int $Mode    A bit set \vDesk\IO\Stream\Mode values that specify whether a file is created if one does not exist, and determines
     *                     whether the contents of existing files are retained or overwritten.
     *
     * @return \vDesk\IO\FileStream A Stream to the file of the FileInfo.
     */
    public function Open(int $Mode = Mode::Read | Mode::Binary): FileStream {
        return File::Open($this->Path, $Mode);
    }
    
}

