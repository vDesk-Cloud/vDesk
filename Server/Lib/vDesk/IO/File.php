<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Mode;

/**
 * Provides static methods for creating, copying, deleting, moving, and opening files.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class File {

    /**
     * Enumeration of invalid filename-characters.
     */
    public const InvalidChars = Path::InvalidChars;

    /**
     * Prevent instantiation.
     */
    private function __construct() {
    }

    /**
     * Creates or overwrites a file in the specified path.
     *
     * @param string $File     The path and name of the file to create.
     * @param bool   $Override Determines whether an existing file will be overridden.
     *
     * @return FileStream A FileStream that provides read/write access to the created file.
     */
    public static function Create(string $File, bool $Override = false): FileStream {
        return self::Open(
            $File,
            $Override
                ? Mode::Truncate | Mode::Duplex | Mode::Binary
                : Mode::Create | Mode::Duplex | Mode::Binary
        );
    }

    /**
     * Opens a FileStream on the specified path with read/write access.
     *
     * @param string $File The file to open.
     * @param int    $Mode A bit set of vDesk\IO\Stream\Mode values that specifies whether a file is created if one does not exist, and determines
     *                     whether the contents of existing files are retained or overwritten.
     *
     * @return FileStream A FileStream opened in the specified mode and path, with read/write access.
     * @throws DirectoryNotFoundException Thrown if the target directory does not exist.
     *
     */
    public static function Open(string $File, int $Mode = Mode::Read | Mode::Binary): FileStream {
        // Check if the target directory exists.
        if(!Directory::Exists($TargetPath = Path::GetPath($File))) {
            throw new DirectoryNotFoundException("The directory at '$TargetPath' does not exist.");
        }
        return new FileStream($File, $Mode);
    }

    /**
     * Opens an existing file for reading.
     *
     * @param string $File The file to be opened for reading.
     *
     * @return FileStream A read-only FileStream on the specified path.
     */
    public static function OpenRead(string $File): FileStream {
        return self::Open($File, Mode::Read | Mode::Binary);
    }

    /**
     * Opens an existing file or creates a new file for writing.
     *
     * @param string $File The file to be opened for writing.
     *
     * @return FileStream A FileStream pointing on the specified path with Write access.
     */
    public static function OpenWrite(string $File): FileStream {
        return self::Open($File, Mode::Read | Mode::Create | Mode::Duplex | Mode::Binary);
    }

    /**
     * Moves an existing file to a new destination.
     *
     * @param string $File        The file to move.
     * @param string $Destination The path of the new destination.
     *
     * @throws FileNotFoundException Thrown if the file to move does not exist.
     * @throws DirectoryNotFoundException Thrown if the target directoy does not exist.
     * @throws IOException Thrown if an equal named file in the target directory already exists.
     * @throws IOException Thrown if the move operation failed.
     */
    public static function Move(string $File, string $Destination): void {
        // Check if the file to move exists.
        if(!self::Exists($File)) {
            throw new FileNotFoundException("The file '$File' does not exist.");
        }
        // Check if the target directory exists.
        if(!Directory::Exists($TargetPath = Path::GetPath($Destination))) {
            throw new DirectoryNotFoundException("The directory at '$TargetPath' does not exist.");
        }
        // Check if an equal named file in the target directory already exists.
        if(self::Exists($TargetPath .= Path::Separator . Path::GetFileName($File, true))) {
            throw new IOException("The target file '$TargetPath' already exists.");
        }
        // Check if the movement failed.
        if(!@\rename($File, $TargetPath)) {
            throw new IOException("Cannot move file from '$File' to '$TargetPath'.");
        }
    }

    /**
     * Copies an existing file to a new file.
     *
     * @param string $File        The file to copy.
     * @param string $Destination The path of the destination directory.
     *
     * @throws FileNotFoundException Thrown if the file to copy does not exist.
     * @throws DirectoryNotFoundException Thrown if the target directoy does not exist.
     * @throws IOException Thrown if an equal named file in the target directory already exists.
     * @throws IOException Thrown if the copy operation failed.
     * @throws \InvalidArgumentException Thrown if the specified paths contain one or more of the invalid characters defined in
     *                                   {@see File::InvalidChars}.
     */
    public static function Copy(string $File, string $Destination): void {
        // Check if the file to copy exists.
        if(!self::Exists($File)) {
            throw new FileNotFoundException("The file '$File' does not exist.");
        }
        // Check if the target directory exists.
        if(!Directory::Exists($TargetPath = Path::GetPath($Destination))) {
            throw new DirectoryNotFoundException("The directory at '$TargetPath' does not exist.");
        }
        // Check if an equal named file in the target directory already exists.
        if(self::Exists($TargetPath .= Path::Separator . Path::GetFileName($Destination, true))) {
            throw new IOException("The target file '$TargetPath' already exists.");
        }
        // Check if the movement failed.
        if(!@\copy($File, $TargetPath)) {
            throw new IOException("Cannot copy file from '$File' to '$TargetPath'.");
        }
    }

    /**
     * Deletes the specified file.
     *
     * @param string $File The file to delete.
     *
     * @throws FileNotFoundException Thrown if the file to delete does not exist.
     * @throws IOException Thrown if the delete operation failed.
     * @throws \InvalidArgumentException Thrown if the specified path contains one or more of the invalid characters defined in
     *                                   {@see File::InvalidChars}.
     */
    public static function Delete(string $File): void {
        // Check if the file to delete exists.
        if(!self::Exists($File)) {
            throw new FileNotFoundException("The file '$File' does not exist.");
        }
        // Check if the deletion failed.
        if(!@\unlink($File)) {
            throw new IOException("Cannot delete file '$File'.");
        }
    }

    /**
     * Renames a file to a specified new name.
     *
     * @param string $File The file to rename.
     * @param string $Name The new name of the specified file.
     *
     * @throws FileNotFoundException Thrown if the file to rename does not exist.
     * @throws IOException Thrown if the rename operation failed.
     * @throws \InvalidArgumentException Thrown if the specified path contains one or more of the invalid characters defined in
     *                                   {@see File::InvalidChars}.
     */
    public static function Rename(string $File, string $Name): void {
        // Check if the file to rename exists.
        if(!self::Exists($File)) {
            throw new FileNotFoundException("The file '$File' does not exist.");
        }
        // Check if an equal named file in the target directory already exists.
        if(self::Exists($TargetPath = Path::GetPath($File) . Path::Separator . $Name)) {
            throw new IOException("Cannot rename file '$File' to '$Name' because a file with the specified name already exists.");
        }
        // Check if the renaming failed.
        if(!@\rename($File, $TargetPath)) {
            throw new IOException("Cannot rename file '$File' to '$Name'.$TargetPath");
        }
    }

    /**
     * Determines whether the specified file exists.
     *
     * @param string $File The path to a file to check for existence.
     *
     * @return bool True if the file exists; otherwise, false.
     */
    public static function Exists(string $File): bool {
        return \is_file($File);
    }

    /**
     * Determines whether the specified file exists and is readable.
     *
     * @param string $File The path to a file to check for readability.
     *
     * @return bool True if the file exists and is readable; otherwise, false.
     */
    public static function IsReadable(string $File): bool {
        return \is_readable($File);
    }

    /**
     * Determines whether the specified file exists and is writable.
     *
     * @param string $File The path to a file to check for writability.
     *
     * @return bool True if the file exists and is writable; otherwise, false.
     */
    public static function IsWritable(string $File): bool {
        return \is_writable($File);
    }

    /**
     * Determines the size of a file in bytes.
     *
     * @param string $File The path to the file whose size to determine.
     *
     * @return null|int The size of the specified file in bytes, or null if the file doesn't exist.
     */
    public static function Size(string $File): ?int {
        return self::Exists($File) ? \filesize($File) : null;
    }

    /**
     * Reads the lines of a file.
     *
     * @param string $File The file to read.
     *
     * @return iterable All the lines of the file.
     * @throws \InvalidArgumentException Thrown if the specified path contains one or more of the invalid characters defined in
     *                                   {@see File::InvalidChars}.
     * @throws FileNotFoundException Thrown if the file to read does not exist.
     */
    public static function ReadLines(string $File): iterable {
        // Check if the file to delete exists.
        if(!self::Exists($File)) {
            throw new FileNotFoundException("The file '$File' does not exist.");
        }
        return \file($File, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
    }

    /**
     * Writes the lines of an array to a file.
     *
     * @param string   $File  The file to write.
     * @param iterable $Lines The lines to write to the specified file.
     *
     * @throws FileNotFoundException Thrown if the file to write does not exist.
     * @throws \InvalidArgumentException Thrown if the specified path contains one or more of the invalid characters defined in
     *                                   {@see File::InvalidChars}.
     */
    public static function WriteLines(string $File, iterable $Lines): void {
        // Check if the file to delete exists.
        if(!self::Exists($File)) {
            throw new FileNotFoundException("The file '$File' does not exist.");
        }
        $FileStream = new FileStream($File, Mode::Create | Mode::Duplex | Mode::Binary);
        foreach($Lines as $Line) {
            $FileStream->Write($Line . \PHP_EOL);
        }
        $FileStream->Close();
    }

}