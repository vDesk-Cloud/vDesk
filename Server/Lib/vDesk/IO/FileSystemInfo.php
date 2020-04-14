<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Struct\Properties;

/**
 * Provides the base class for both FileInfo and DirectoryInfo objects.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
abstract class FileSystemInfo {
    
    use Properties;
    
    /**
     * The path to the current file or directory.
     *
     * @var string
     */
    protected string $Path;
    
    /**
     * Gets an instance of the parent directory.
     *
     * @var DirectoryInfo|null
     */
    protected ?DirectoryInfo $Directory = null;
    
    /**
     * Gets or sets the attributes for the current file or directory.
     *
     * @var string
     */
    protected string $Attributes;
    
    /**
     * Gets or sets the creation time of the current file or directory.
     *
     * @var \DateTime|null
     */
    protected ?\DateTime $CreationTime = null;
    
    /**
     * Gets a value indicating whether the file or directory exists.
     *
     * @var bool|null
     */
    protected ?bool $Exists = null;
    
    /**
     * Gets the string representing the extension part of the file.
     *
     * @var string|null
     */
    protected ?string $Extension = null;
    
    /**
     * For files, gets the name of the file.
     * For directories, gets the name of the last directory in the hierarchy if a hierarchy exists.
     * Otherwise, the Name property gets the name of the directory.
     *
     * @var string|null
     */
    protected ?string $Name = null;
    
    /**
     * Gets the full path of the directory or file.
     *
     * @var string|null
     */
    protected ?string $FullName = null;
    
    /**
     * Gets or sets the time the current file or directory was last accessed.
     *
     * @var \DateTime|null
     */
    protected ?\DateTime $LastAccessTime = null;
    
    /**
     * Gets or sets the time when the current file or directory was last written to.
     *
     * @var \DateTime|null
     */
    protected ?\DateTime $LastWriteTime = null;
    
    /**
     * Gets the size of the FileSystemInfo.
     *
     * @var int|null
     */
    protected ?int $Size = null;
    
    /**
     * Initializes a new instance of the FileSystemInfo class, which acts as a wrapper for a file or directory path.
     *
     * @param string $Path The fully qualified name of the new file or directory, or the relative file or directory name.
     */
    public abstract function __construct(string $Path);
    
    /**
     * Creates a file or directory.
     */
    public abstract function Create();
    
    /**
     * Deletes a file or directory.
     *
     * @return bool True on success, false on failure.
     */
    public abstract function Delete(): bool;
    
    /**
     * Refreshes the state of the object.
     */
    public abstract function Refresh(): void;
    
}

