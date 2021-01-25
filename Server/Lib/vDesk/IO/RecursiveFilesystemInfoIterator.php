<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Represents an iterator that iterates recursively over the contents of a directory.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class RecursiveFilesystemInfoIterator implements \IteratorAggregate {
    
    /**
     * The base DirectoryInfo of the RecursiveFilesystemInfoIterator.
     *
     * @var null|\vDesk\IO\DirectoryInfo
     */
    protected ?DirectoryInfo $Directory;
    
    /**
     * Flag for yielding files first.
     */
    public const FilesFirst = 0;
    
    /**
     * Flag for yielding directories first.
     */
    public const DirectoriesFirst = 1;
    
    /**
     * The mode of the RecursiveFilesystemInfoIterator.
     *
     * @var bool
     */
    protected bool $FilesFirst;
    
    /**
     * The path separatur to use.
     *
     * @var string
     */
    protected string $Separator;
    
    /**
     * Initializes a new instance of the RecursiveFilesystemInfoIterator class.
     *
     * @param DirectoryInfo $Directory  Initializes the RecursiveFilesystemInfoIterator with the directory to iterate over.
     * @param bool          $FilesFirst Flag indicating whether to iterate first over files.
     * @param string        $Separator  Initializes the RecursiveFilesystemInfoIterator with the path separator to use.
     */
    public function __construct(DirectoryInfo $Directory, bool $FilesFirst = true, string $Separator = Path::Separator) {
        $this->Directory  = $Directory;
        $this->FilesFirst = $FilesFirst;
        $this->Separator  = $Separator;
    }
    
    /**
     * @return \Generator
     * @ignore
     */
    public function getIterator(): \Generator {
        $Separator = $this->Separator;
        $Generator = $this->FilesFirst
            ? static function(DirectoryInfo $Directory) use (&$Generator, $Separator): \Generator {
                foreach($Directory->IterateFiles($Separator) as $File) {
                    yield $File;
                }
                foreach($Directory->IterateDirectories($Separator) as $Folder) {
                    yield $Folder;
                    yield from $Generator($Folder);
                }
            }
            : static function(DirectoryInfo $Directory) use (&$Generator, $Separator): \Generator {
                foreach($Directory->IterateFileSystemEntries($Separator) as $Entry) {
                    yield $Entry;
                    if($Entry instanceof DirectoryInfo) {
                        yield from $Generator($Entry);
                    }
                }
            };
        yield from $Generator($this->Directory);
    }
    
}

