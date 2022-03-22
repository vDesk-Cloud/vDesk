<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Struct\Collections\Collection;

/**
 * Provides static methods for creating, copying, deleting, moving, and opening directories.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Directory {

    /**
     * Prevent instantiation.
     */
    private function __construct() {
    }

    /**
     * Creates a directory in the specified path.
     *
     * @param string $Path The path and name of the directory to create.
     *
     * @return DirectoryInfo An object that represents the directory at the specified path.
     * This object is returned regardless of whether a directory at the specified path already exists.
     * @throws \vDesk\IO\IOException Thrown if the create operation failed.
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the target directoy does not exist.
     */
    public static function Create(string $Path): DirectoryInfo {
        // Check if the target directory exists.
        if(!self::Exists($TargetPath = Path::GetDirectory($Path))) {
            throw new DirectoryNotFoundException("The directory at '$TargetPath' does not exist.");
        }
        // Check if the target already directory exists and if the creation failed..
        if(!self::Exists($Path) && !@\mkdir($Path)) {
            throw new IOException("Cannot create directory at '$Path'.");
        }
        return new DirectoryInfo($Path);
    }

    /**
     * Moves an existing directory to a new destination.
     *
     * @param string $Directory   The directory to move.
     * @param string $Destination The path of the new destination.
     * @param string $Separator   The path separator to use.
     *
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the target directoy does not exist.
     * @throws \vDesk\IO\IOException Thrown if the move operation failed.
     */
    public static function Move(string $Directory, string $Destination, string $Separator = Path::Separator): void {
        // Check if the directory to move exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        // Check if the target directory exists.
        if(!self::Exists($TargetPath = Path::GetPath($Destination))) {
            throw new DirectoryNotFoundException("The directory at '$TargetPath' does not exist.");
        }
        // Check if an equal named directory in the target directory already exists.
        if(self::Exists($TargetPath .= $Separator . Path::GetFileName($Directory, true))) {
            throw new IOException("The target directory '$TargetPath' already exists.");
        }
        // Check if the movement failed.
        if(!@\rename($Directory, $TargetPath)) {
            throw new IOException("Cannot move directory from '$Directory' to '$TargetPath'.");
        }
    }

    /**
     * Copies an existing directory to a new directory.
     *
     * @param string $Directory   The directory to copy.
     * @param string $Destination The path of the destination directory.
     * @param string $Separator   The path separator to use.
     *
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the target directoy does not exist.
     * @throws \vDesk\IO\IOException Thrown if the copy operation failed.
     */
    public static function Copy(string $Directory, string $Destination, string $Separator = Path::Separator): void {
        // Check if the directory to copy exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        // Check if the target directory exists.
        if(!self::Exists($TargetPath = Path::GetPath($Destination))) {
            throw new DirectoryNotFoundException("The directory at '$TargetPath' does not exist.");
        }
        // Check if an equal named directory in the target directory already exists.
        if(self::Exists($TargetPath .= $Separator . Path::GetFileName($Directory, true))) {
            throw new IOException("The target directory '$TargetPath' already exists.");
        }
        // Check if the movement failed.
        if(!@\copy($Directory, $TargetPath)) {
            throw new IOException("Cannot copy directory from '$Directory' to '$TargetPath'.");
        }
    }

    /**
     * Deletes the specified directory.
     *
     * @param string $Directory The directory to delete.
     * @param bool   $Children  Determines whether to delete the specified directory, its subdirectories, and all files.
     *
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the directory to delete does not exist.
     * @throws \vDesk\IO\IOException Thrown if the delete operation failed.
     */
    public static function Delete(string $Directory, bool $Children = false): void {
        // Check if the directory to delete exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }

        if(!self::IsWritable($Directory)) {
            throw new DirectoryNotFoundException("Cannot delete directory '$Directory'. The directory is not writable.");
        }

        if($Children === true) {
            // Delete children.
            foreach((new DirectoryInfo($Directory))->IterateFileSystemEntries() as $Entry) {
                if($Entry instanceof FileInfo) {
                    File::Delete($Entry->FullName);
                } else if($Entry instanceof DirectoryInfo) {
                    self::Delete($Entry->FullName, $Children);
                }
            }
        } else if(\count(\scandir($Directory, \SCANDIR_SORT_NONE)) > 2) {
            throw new IOException("Cannot delete directory '$Directory'. The directory is not empty.");
        }
        if(!@\rmdir($Directory)) {
            throw new IOException("Cannot delete directory '$Directory'.");
        }
    }

    /**
     * Renames a directory to a specified new name.
     *
     * @param string $Directory The directory to rename.
     * @param string $Name      The new name of the specified directory.
     * @param string $Separator The path separator to use.
     *
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the directory to rename does not exist.
     * @throws \vDesk\IO\IOException Thrown if the rename operation failed.
     */
    public static function Rename(string $Directory, string $Name, string $Separator = Path::Separator): void {
        // Check if the directory to rename exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        // Check if an equal named directory in the target directory already exists.
        if(self::Exists($TargetPath = Path::GetPath($Directory) . $Separator . $Name)) {
            throw new IOException("Cannot rename directory '$Directory' to '$Name' because a directory with the specified name already exists.");
        }
        // Check if the renaming failed.
        if(!@\rename($Directory, $TargetPath)) {
            throw new IOException("Cannot rename directory '$Directory' to '$Name'.");
        }
    }

    /**
     * Determines whether the specified directory exists.
     *
     * @param string $Directory The path to a directory to check for existance.
     *
     * @return bool True if the directory exists; otherwise, false.
     */
    public static function Exists(string $Directory): bool {
        return \is_dir($Directory);
    }

    /**
     * Determines whether the specified directory exists and is readable.
     *
     * @param string $Directory The path to a directory to check for readability.
     *
     * @return bool True if the directory exists and is readable; otherwise, false.
     */
    public static function IsReadable(string $Directory): bool {
        return \is_readable($Directory);
    }

    /**
     * Determines whether the specified directory exists and is writable.
     *
     * @param string $Directory The path to a directory to check for writability.
     *
     * @return bool True if the directory exists and is writable; otherwise, false.
     */
    public static function IsWritable(string $Directory): bool {
        return \is_writable($Directory);
    }

    /**
     * Gets the amount of child folders and files of the specified directory.
     *
     * @param string $Directory The path to the directory.
     *
     * @return null|int The amount of child files and folders, or null if the directory doesn't exist.
     */
    public static function Size(string $Directory): ?int {
        if(self::Exists($Directory)) {
            return self::GetFileSystemEntries($Directory)->Count;
        }
        return null;
    }

    /**
     * Returns the names of files (including their paths) in the specified directory.
     *
     * @param string $Directory The relative or absolute path to the directory to search.
     * @param string $Separator The path separator to use.
     *
     * @return Collection A Collection of the full names (including paths) for the files in the specified directory, or an empty Collection
     *                          if no files are found.
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the directory to scan does not exist.
     */
    public static function GetFiles(string $Directory, string $Separator = Path::Separator): Collection {
        //Check if the directory to scan exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        $Stream = new DirectoryStream($Directory);
        $Files  = new Collection();
        while(!$Stream->EndOfStream()) {
            if(File::Exists($File = $Directory . $Separator . $Stream->Read())) {
                $Files->Add($File);
            }
        }
        $Stream->Close();
        return $Files;
    }

    /**
     * Returns the names of subdirectories (including their paths) in the specified directory.
     *
     * @param string $Directory The relative or absolute path to the directory to search.
     * @param string $Separator The path separator to use.
     *
     * @return Collection A Collection of the full names (including paths) of subdirectories in the specified path, or an empty Collection
     *                          if no directories are found.
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the directory to scan does not exist.
     */
    public static function GetDirectories(string $Directory, string $Separator = Path::Separator): Collection {
        // Check if the directory to scan exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        $Stream      = new DirectoryStream($Directory);
        $Directories = new Collection();
        while(!$Stream->EndOfStream()) {
            if(self::Exists($SubDirectory = $Directory . $Separator . $Stream->Read())) {
                $Directories->Add($SubDirectory);
            }
        }
        $Stream->Close();
        return $Directories;
    }

    /**
     * Returns the names of all files and subdirectories in a specified path.
     *
     * @param string $Directory The relative or absolute path to the directory to search.
     * @param string $Separator The path separator to use.
     *
     * @return Collection A Collection of the names of files and subdirectories in the specified directory, or an empty Collection if no
     *                          files or subdirectories are found.
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the directory to scan does not exist.
     */
    public static function GetFileSystemEntries(string $Directory, string $Separator = Path::Separator): Collection {
        $Directory = Path::GetPath($Directory);
        // Check if the directory to scan exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        $Stream  = new DirectoryStream($Directory);
        $Entries = new Collection();
        while(!$Stream->EndOfStream()) {
            $Entries->Add($Directory . $Separator . $Stream->Read());
        }
        $Stream->Close();
        return $Entries;
    }

    /**
     * Returns a Generator that iterates over the names of files (including their paths) in the specified directory.
     *
     * @param string $Directory The relative or absolute path to the directory to search.
     * @param string $Separator The path separator to use.
     *
     * @return \Generator A Generator that iterates over the names of the full names (including paths) for the files in the specified
     *                          directory.
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the directory to scan does not exist.
     */
    public static function IterateFiles(string $Directory, string $Separator = Path::Separator): \Generator {
        $Directory = Path::GetPath($Directory);
        // Check if the directory to scan exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        // Fetch files.
        $Stream = new DirectoryStream($Directory);
        while(!$Stream->EndOfStream()) {
            if(!File::Exists($File = $Directory . $Separator . $Stream->Read())) {
                continue;
            }
            yield $File;
        }
        $Stream->Close();
    }

    /**
     * Returns a Generator that iterates over the names of subdirectories (including their paths) in the specified directory.
     *
     * @param string $Directory The relative or absolute path to the directory to search.
     * @param string $Separator The path separator to use.
     *
     * @return \Generator A Generator that iterates over the full names (including paths) of subdirectories in the specified path.
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the directory to scan does not exist.
     */
    public static function IterateDirectories(string $Directory, string $Separator = Path::Separator): \Generator {
        $Directory = Path::GetPath($Directory);
        // Check if the directory to scan exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        // Fetch directories.
        $Stream = new DirectoryStream($Directory);
        while(!$Stream->EndOfStream()) {
            if(!self::Exists($SubDirectory = $Directory . $Separator . $Stream->Read())) {
                continue;
            }
            yield $SubDirectory;
        }
        $Stream->Close();
    }

    /**
     * Returns a Generator that iterates over the names of all files and subdirectories in a specified path.
     *
     * @param string $Directory The relative or absolute path to the directory to search.
     * @param string $Separator The path separator to use.
     *
     * @return \Generator A Generator that iterates over the names of files and subdirectories in the specified directory.
     * @throws \vDesk\IO\DirectoryNotFoundException Thrown if the directory to scan does not exist.
     */
    public static function IterateFileSystemEntries(string $Directory, string $Separator = Path::Separator): \Generator {
        $Directory = Path::GetPath($Directory);
        // Check if the directory to scan exists.
        if(!self::Exists($Directory)) {
            throw new DirectoryNotFoundException("The directory '$Directory' does not exist.");
        }
        // Fetch entries.
        $Stream = new DirectoryStream($Directory);
        while(!$Stream->EndOfStream()) {
            yield $Directory . $Separator . $Stream->Read();
        }
        $Stream->Close();
    }

}