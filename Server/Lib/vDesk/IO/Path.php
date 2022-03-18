<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Struct\Text;

/**
 * Performs operations on strings that contain file or directory path information.
 * These operations are performed in a cross-platform manner.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Path {

    /**
     * Represents a platform specific directory separator of the underlying OS.
     */
    public const Separator = \DIRECTORY_SEPARATOR;

    /**
     * Represents a platform specific directory separator of *Nix-based systems.
     */
    public const UnixSeparator = "/";

    /**
     * Represents a platform specific directory separator of NT-based systems.
     */
    public const NTSeparator = "\\";

    /**
     * Represents a platform specific volume separator 'C:\'.
     */
    public const VolumeSeparator = ":";

    /**
     * The maximum supporteded length of a path.
     */
    public const MaxLength = \PHP_MAXPATHLEN;

    /**
     * Enumeration of invalid path characters.
     */
    public const InvalidChars = [
        "\x0",
        "\x1",
        "\x2",
        "\x3",
        "\x4",
        "\x5",
        "\x6",
        "\x7",
        "\x8",
        "\x9",
        "\xA",
        "\xB",
        "\xC",
        "\xD",
        "\xE",
        "\xF",
        "\x10",
        "\x11",
        "\x12",
        "\x13",
        "\x14",
        "\x15",
        "\x16",
        "\x17",
        "\x18",
        "\x19",
        "\x1A",
        "\x1B",
        "\x1C",
        "\x1D",
        "\x1E",
        "\x1F",
        "|",
        "<",
        ">",
        "*",
        "?",
        ":",
        "\""
    ];

    /**
     * Prevent instantiation.
     */
    private function __construct() {
    }

    /**
     * Determines whether the specified file or directory exists.
     *
     * @param string $Path The path to a file or directory to check for existance.
     *
     * @return bool True if the file or directory exists; otherwise, false.
     */
    public static function Exists(string $Path): bool {
        return \file_exists($Path);
    }

    /**
     * Normalizes and formats a specified set of path string.
     *
     * @param string ...$Parts One or more strings which represents the path to normalize.
     *
     * @return string The normalized and formatted representation of the specified path excluding any invalid characters.
     */
    public static function Normalize(string ...$Parts): string {
        $Chunks = [];

        //Normalize directory separator for splitting.
        foreach(
            Text::Join(Text::Empty, $Parts)
                ->Replace(self::NTSeparator, self::UnixSeparator)
                ->Split(self::UnixSeparator) as $Chunk
        ) {
            if(!$Chunk->IsNullOrWhitespace()) {
                $Chunks[] = $Chunk;
            }
        }

        //Check if the first part of the path contains a volume separator.
        if(isset($Chunks[0]) && $Chunks[0]->IndexOf(self::VolumeSeparator) === 1) {
            //Check if the volume separator is followed by any characters.
            if($Chunks[0]->Length > 2) {
                $VolumeSeparator = Text::Substring($Chunks[0], 0, 2);
                $Path            = Text::Substring($Chunks[0], 2)->ReplaceAny(self::InvalidChars, Text::Empty);
                $Chunks[0]       = $Path->IsNullOrWhitespace() ? Text::Concat((string)$VolumeSeparator) : Text::Concat((string)$VolumeSeparator, self::Separator, (string)$Path);
            }
        } else {
            $Chunks[0] = self::Separator . $Chunks[0]->ReplaceAny(self::InvalidChars, Text::Empty);
        }

        // Remove invalid characters.
        for($i = 1, $l = \count($Chunks); $i < $l; $i++) {
            $Chunks[$i]->ReplaceAny(self::InvalidChars, Text::Empty);
        }

        return (string)Text::Join(self::Separator, $Chunks);
    }

    /**
     * Checks if the specified path string contains any invalid characters or is too long.
     *
     * @param string $Path The path to check.
     *
     * @throws PathTooLongException Thrown if the length of the specified path exceeds the maximum supported length of paths
     *                              {@see Path::MaxLength}.
     * @throws \InvalidArgumentException Thrown if the specified path contains one or more of the invalid characters defined in
     *                                   {@see Path::InvalidChars}.
     */
    public static function Validate(string $Path): void {
        // Check if the specified path exceeds the maximum supported path length.
        if(Text::Length($Path) > self::MaxLength) {
            throw new PathTooLongException("The specified path exceeds the maximum supported length of " . self::MaxLength);
        }
        // Check if the specified path contains any invalid characters.
        if(Text::ContainsAny(Text::IndexOf($Path, self::VolumeSeparator) === 1 ? Text::Substring($Path, 2) : $Path, self::InvalidChars)) {
            throw new \InvalidArgumentException("\$Path contains one or more of the invalid characters defined in Path::InvalidChars.");
        }
    }

    /**
     * Returns the path of the parent directory of the specified path string.
     *
     * @param string $Path The path of a file or directory.
     *
     * @return string|null The name of the directory of the specified path, or null if the specified path does not have directory
     *                     information.
     */
    public static function GetDirectory(string $Path): ?string {
        return Text::IsNullOrWhitespace($DirectoryName = \pathinfo($Path, \PATHINFO_DIRNAME)) ? null : $DirectoryName;
    }

    /**
     * Returns the name of the directory of the specified path string.
     *
     * @param string $Path The path of a file or directory.
     *
     * @return string|null The name of the directory of the specified path, or null if the specified path does not have directory
     *                     information.
     */
    public static function GetDirectoryName(string $Path): ?string {
        return Text::IsNullOrWhitespace($DirectoryName = \pathinfo($Path, \PATHINFO_BASENAME)) ? null : $DirectoryName;
    }

    /**
     * Returns the extension of the specified path string.
     *
     * @param string $Path The path string from which to get the extension.
     *
     * @return string|NULL The extension of the specified path (excluding the period "."), or null if the specified path does not have
     *                     extension information.
     */
    public static function GetExtension(string $Path): ?string {
        return Text::IsNullOrWhitespace($Extension = \pathinfo($Path, \PATHINFO_EXTENSION)) ? null : $Extension;
    }

    /**
     * Returns the file name of the specified path string.
     *
     * @param string $Path          string $Path The path string from which to get the file name.
     * @param bool   $WithExtension Determines whether the name of the specified file contains its extension.
     *
     * @return string|null The characters after the last directory character in the specified path.
     */
    public static function GetFileName(string $Path, bool $WithExtension = true): ?string {
        return Text::IsNullOrWhitespace($FileName = \pathinfo($Path, $WithExtension ? \PATHINFO_BASENAME : \PATHINFO_FILENAME)) ? null : $FileName;
    }

    /**
     * Returns the path for the specified path string exluding any trailing filename.
     *
     * @param string $Path The file or directory for which to obtain path information.
     *
     * @return string The path excluding any trailing filename of the specified path string.
     */
    public static function GetPath(string $Path): string {
        if(Directory::Exists($Path)) {
            return $Path;
        }
        return self::GetDirectory($Path);
    }

    /**
     * Returns the absolute path for the specified path string.
     *
     * @param string $Path The file or directory for which to obtain absolute path information.
     *
     * @return string|NULL The fully qualified location of the specified path, or null if the path doesn't exist.
     */
    public static function GetFullPath(string $Path): ?string {
        return ($FullPath = \realpath($Path)) !== false ? $FullPath : null;
    }

    /**
     * Changes the extension of the specified path string.
     *
     * @param string $Path      The path information to modify.
     * @param string $Extension The new extension.
     *
     * @return string The modified path information.
     */
    public static function ChangeExtension(string $Path, string $Extension): string {
        return (string)Text::Replace($Path, self::GetExtension($Path) ?? Text::Empty, $Extension);
    }

    /**
     * Determines wehther the specified path string includes a file name extension.
     *
     * @param string $Path The path to search for an extension.
     *
     * @return bool True if the characters that follow the last directory separator (\\ or /) or volume separator (:) in the path include a
     *              period (.)
     */
    public static function HasExtension(string $Path): bool {
        return !Text::IsNullOrWhitespace(\pathinfo($Path, \PATHINFO_EXTENSION));
    }

    /**
     * Returns the path of the temporary folder.
     *
     * @return string The path to the temporary folder.
     */
    public static function GetTempPath(): string {
        return \sys_get_temp_dir();
    }

}