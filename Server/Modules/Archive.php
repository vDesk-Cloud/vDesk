<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Archive\Elements;
use vDesk\Archive\Element;
use vDesk\Archive\Element\Created;
use vDesk\Archive\Element\Deleted;
use vDesk\Archive\Element\Moved;
use vDesk\Archive\Element\Renamed;
use vDesk\Archive\Thumbnail;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\Functions;
use vDesk\Modules\Command;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\FileStream;
use vDesk\IO\Path;
use vDesk\IO\Stream\Mode;
use vDesk\Modules\Module;
use vDesk\Search\Results;
use vDesk\Search\ISearch;
use vDesk\Search\Result;
use vDesk\Security\AccessControlList;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Struct\Guid;
use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Text;
use vDesk\Utils\Log;

/**
 * Archive Module.
 *
 * @package vDesk\Archive
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @todo    Implement chunked base64 filestream for streaming files through XMLHTTPRequest.
 * @todo    Implement Import and export functionality of either whole Archive or specific directories.
 * @todo    Implement method to resolve a /-separated path.
 */
final class Archive extends Module implements ISearch {

    /**
     * The ID of the root Element of the Archive.
     */
    public const Root = 1;

    /**
     * The ID of the system-folder of the Archive.
     */
    public const System = 2;

    /**
     * Gets a filled Element.
     *
     * @param null|int $ID The ID of the Element to get.
     *
     * @return \vDesk\Archive\Element The filled Element.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have read-permissions on the Element to get.
     */
    public static function GetElement(int $ID = null): Element {
        $Element = (new Element($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Element->AccessControlList->Read) {
            throw new UnauthorizedAccessException();
        }
        return $Element;
    }

    /**
     * Returns a Collection all readable child Elements of a specified folder Element.
     *
     * @param null|int $ID The ID of the parent folder Element to get the child Elements of.
     *
     * @return \vDesk\Archive\Elements A Collection containing all child Elements of the specified folder Element the current User has read permissions on.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have read-permissions on the target parent Element.
     */
    public static function GetElements(int $ID = null): Elements {
        $Parent = new Element($ID ?? Command::$Parameters["ID"]);
        if(!$Parent->AccessControlList->Read) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to get child Elements without having permissions.");
            throw new UnauthorizedAccessException();
        }

        $Elements = new Elements();
        foreach(
            Expression::Select("*")
                      ->From("Archive.Elements")
                      ->Where(["Parent" => $Parent])
            as
            $Element
        ) {
            $Element = new Element(
                (int)$Element["ID"],
                new User((int)$Element["Owner"]),
                $Parent,
                $Element["Name"],
                (int)$Element["Type"],
                new \DateTime($Element["CreationTime"]),
                $Element["Guid"],
                $Element["Extension"],
                $Element["File"],
                (int)$Element["Size"],
                $Element["Thumbnail"],
                new AccessControlList([], (int)$Element["AccessControlList"])
            );
            if($Element->AccessControlList->Read) {
                $Elements->Add($Element);
            }
        }
        return $Elements;
    }

    /**
     * Returns a hierarchical branch of IDs from a specified destination Element starting from the Archive root Element.
     *
     * @param null|int $ID The ID of the destination Element to get the branch of.
     *
     * @return int[] An array containing the IDs of a branch-hierarchy starting from the Archive to the specified Element.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have read-permissions on the target Element.
     */
    public static function GetBranch(int $ID = null): array {
        $Element = (new Element($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Element->AccessControlList->Read) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to fetch branch of Element without having permissions.");
            throw new UnauthorizedAccessException();
        }

        $Elements = [$Element->ID];
        do {
            //Check if user can view the Element.
            if(!$Element->Parent->AccessControlList->Read) {
                Log::Warn(__METHOD__, User::$Current->Name . " tried to fetch branch of Element without having permissions.");
                throw new UnauthorizedAccessException();
            }
            $Elements[] = $Element->Parent->ID;
            $Element    = $Element->Parent;
        } while($Element->ID > self::Root);
        return \array_reverse($Elements);
    }

    /**
     * Uploads a file to the Archive.
     * Triggers the {@link \vDesk\Archive\Element\Created}-Event.
     *
     * @param int|null                $ID   The ID of the target folder Element.
     * @param string|null             $Name The name of the file to upload.
     * @param \vDesk\IO\FileInfo|null $File The file to upload.
     *
     * @return \vDesk\Archive\Element The uploaded Element.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write-permissions on the target folder Element.
     */
    public static function Upload(int $ID = null, string $Name = null, FileInfo $File = null): Element {
        $Parent = new Element($ID ?? Command::$Parameters["Parent"]);
        $Name   ??= Command::$Parameters["Name"];
        $File   ??= Command::$Parameters["File"];

        //Check if the User can write to the directory.
        if(!$Parent->AccessControlList->Write) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to upload a file without having permissions.");
            throw new UnauthorizedAccessException();
        }

        //$File->Move(Settings::$Local["Archive"]["Directory"]);
        //$File->Rename(\uniqid("", true));

        //Extract the extension of the file.
        $Extension = \strtolower(Path::GetExtension($Name));
        $Filename  = \uniqid("", true) . ".{$Extension}";

        $TargetDirectory = Settings::$Local["Archive"]["Directory"] . Path::Separator;
        $TargetFile      = File::Create($TargetDirectory . $Filename);

        //Save uploaded file.
        $TempFile = $File->Open();
        while(!$TempFile->EndOfStream()) {
            $TargetFile->Write((string)$TempFile->Read());
        }

        //Create new Element.
        $Element = new Element(
            null,
            User::$Current,
            $Parent,
            (string)Text::Substring($Name, 0, Text::LastIndexOf($Name, ".")),
            Element::File,
            new \DateTime("now"),
            Guid::Create(),
            $Extension,
            $Filename,
            File::Size($TargetDirectory . $Filename),
            Thumbnail::Create($TargetDirectory . $Filename),
            new AccessControlList($Parent->AccessControlList)
        );
        $Element->Save();
        (new Created($Element))->Dispatch();
        Log::Info(__METHOD__, User::$Current->Name . " uploaded [{$Element->ID}]({$Element->Name}) to [{$Parent->ID}]({$Parent->Name})");
        return $Element;
    }

    /**
     * Downloads the file of a specified Element.
     *
     * @param int|null $ID The ID of the Element to download the file of.
     *
     * @return \vDesk\IO\FileInfo A FileInfo that represents the file of the specified Element to download.
     * @throws \vDesk\Struct\InvalidOperationException Thrown if the Element to download is not a file.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have read-permissions on the Element to download.
     */
    public static function Download(int $ID = null): FileInfo {
        $Element = (new Element($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Element->AccessControlList->Read) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to download a file Element without having permissions.");
            throw new UnauthorizedAccessException();
        }
        if($Element->Type !== Element::File) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to download a folder Element.");
            throw new InvalidOperationException();
        }
        //Send file.
        $File           = new FileInfo(Settings::$Local["Archive"]["Directory"] . Path::Separator . $Element->File);
        $File->MimeType = Expression::Select("MimeType")
                                    ->From("Archive.MimeTypes")
                                    ->Where(["Extension" => $Element->Extension])();
        return $File;
    }

    /**
     * Creates a new folder Element.
     * Triggers the {@link \vDesk\Archive\Element\Created}-Event for the created folder Element.
     *
     * @param int|null    $ID   The ID of the parent Element of the folder Element to create.
     * @param string|null $Name The name of the folder Element.
     *
     * @return \vDesk\Archive\Element The new created folder Element.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write-permissions on the parent Element.
     */
    public static function CreateFolder(int $ID = null, string $Name = null): Element {
        $Parent = new Element($ID ?? Command::$Parameters["Parent"]);
        if(!$Parent->AccessControlList->Write) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to create a new folder Element without having permissions.");
            throw new UnauthorizedAccessException();
        }

        $Folder = new Element(
            null,
            User::$Current,
            $Parent,
            $Name ?? Command::$Parameters["Name"],
            Element::Folder,
            new \DateTime("now"),
            Guid::Create(),
            null,
            null,
            0,
            null,
            new AccessControlList(
                $Parent->AccessControlList,
                null,
                $Parent->AccessControlList->Read,
                $Parent->AccessControlList->Write,
                $Parent->AccessControlList->Delete
            )
        );
        $Folder->Save();
        Log::Info(__METHOD__, User::$Current->Name . " created new directory [{$Folder->ID}]({$Folder->Name}) in directory [{$Parent->ID}]({$Parent->Name})");
        (new Created($Folder))->Dispatch();
        return $Folder;
    }

    /**
     * Moves a set of Elements to a new destination folder Element.
     * Triggers the {@link \vDesk\Archive\Element\Moved}-Event for every moved Element.
     *
     * @param null|int   $ID       The ID of the target folder Element.
     * @param int[]|null $Elements The IDs of the Elements to move.
     *
     * @return boolean True on success.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write-permissions on the target Element.
     */
    public static function Move(int $ID = null, array $Elements = null): bool {
        $Target = new Element($ID ?? Command::$Parameters["Target"]);
        if(!$Target->AccessControlList->Write) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to move Elements without having permissions.");
            throw new UnauthorizedAccessException();
        }

        //Update the parent entry of the Elements.
        foreach($Elements ?? Command::$Parameters["Elements"] as $ID) {
            $Element = (new Element($ID))->Fill();
            if($Element->ID > self::System && $Element->AccessControlList->Write && $Element->ID !== $Target->ID) {
                $Element->Parent = $Target;
                $Element->Save();
                Log::Info(__METHOD__, User::$Current->Name . " moved [{$Element->ID}]({$Element->Name}) to [{$Target->ID}]({$Target->Name}).");
                (new Moved($Element))->Dispatch();
            }
        }
        return true;
    }

    /**
     * Copies a set of Elements to a new destination folder Element.
     * Triggers the {@link \vDesk\Archive\Element\Created}-Event for each copied Element.
     *
     * @param null|int   $ID       The ID of the target folder Element to copy the specified Elements to.
     * @param int[]|null $Elements The IDs of the Elements to copy to the specified target Element.
     *
     * @return boolean True if the whole tree has been successfully copied.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write-permissions on the target Element.
     * @todo Return the list of new Elements.
     */
    public static function Copy(int $ID = null, array $Elements = null): bool {
        $Target = new Element($ID ?? Command::$Parameters["Target"]);
        //Check if the User can write to the target folder Element.
        if(!$Target->AccessControlList->Write) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to copy Elements to [{$Target->ID}]({$Target->Name}) without having permissions.");
            throw new UnauthorizedAccessException();
        }

        $RecursiveCopy = static function(Element $Target, Element $Element) use (&$RecursiveCopy) {

            if($Target->AccessControlList->Write) {

                //Copy values.
                $CopiedElement = new Element(
                    null,
                    User::$Current,
                    $Target,
                    $Element->Name,
                    $Element->Type,
                    new \DateTime("now"),
                    Guid::Create(),
                    $Element->Extension,
                    null,
                    $Element->Size,
                    $Element->Thumbnail,
                    new AccessControlList($Element->AccessControlList)
                );

                //Check if copied Element is a file.
                if($Element->Type === Element::File) {
                    $CopiedElement->File = \uniqid('', false) . "." . $Element->Extension;
                    File::Copy(
                        Settings::$Local["Archive"]["Directory"] . Path::Separator . $Element->File,
                        Settings::$Local["Archive"]["Directory"] . Path::Separator . $CopiedElement->File
                    );
                }

                //Save new Element.
                $CopiedElement->Save();
                (new Created($CopiedElement))->Dispatch();

                //Check if the Element has children.
                if($Element->HasChildren) {
                    $ChildElements = Elements::FromElement($Element);
                    //Copy the children.
                    foreach($ChildElements as $ChildElement) {
                        if($RecursiveCopy($CopiedElement, $ChildElement)) {
                            Log::Info(
                                __METHOD__,
                                User::$Current->Name . " copied [{$ChildElement->ID}]({$ChildElement->Name}) to [{$CopiedElement->ID}]({$CopiedElement->Name})"
                            );
                        }
                    }
                }
                return true;
            }
            return false;

        };

        //Update the parent entry of the Elements.
        foreach($Elements ?? Command::$Parameters["Elements"] as $ID) {
            $Element = (new Element($ID))->Fill();
            if(($Target->ID !== $Element->ID) && $RecursiveCopy($Target, $Element)) {
                Log::Info(__METHOD__, User::$Current->Name . " moved [{$Element->ID}]({$Element->Name}) to [{$Target->ID}]({$Target->Name})");
            }
        }

        return true;
    }

    /**
     * Renames an Element.
     * Triggers the {@link \vDesk\Archive\Element\Renamed}-Event for the renamed Element.
     *
     * @param null|int    $ID   The ID of the Element to rename.
     * @param null|string $Name The new name of the Element.
     *
     * @return bool True if the Element has been successfully renamed.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write-permissions on the Element to rename.
     */
    public static function Rename(int $ID = null, string $Name = null): bool {
        $Element = (new Element($ID ?? Command::$Parameters["ID"]))->Fill();
        if($Element->ID <= self::System || !$Element->AccessControlList->Write) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to rename Element without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Element->Name = $Name ?? Command::$Parameters["Name"];
        $Element->Save();
        Log::Info(__METHOD__, User::$Current->Name . " renamed Element [{$Element->ID}] to '{$Element->Name}'");
        (new Renamed($Element))->Dispatch();
        return true;
    }

    /**
     * Updates the file contents of a specified Element.
     *
     * @param int|null                $ID   The ID of the Element to update its file of.
     * @param \vDesk\IO\FileInfo|null $File The new file of the Element.
     *
     * @return \vDesk\Archive\Element The updated Element.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write-permissions on the Element to update.
     */
    public static function UpdateFile(int $ID = null, FileInfo $File = null): Element {
        $Element = (new Element($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Element->AccessControlList->Write) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to update file of Element without having permissions.");
            throw new UnauthorizedAccessException();
        }

        $TargetDirectory = Settings::$Local["Archive"]["Directory"] . Path::Separator;

        //Overwrite file.
        $TargetFile = new FileStream($TargetDirectory . $Element->File, Mode::Write | Mode::Truncate | Mode::Binary);
        $TempFile   = ($File ?? Command::$Parameters["File"])->Open();
        while(!$TempFile->EndOfStream()) {
            $TargetFile->Write($TempFile->Read());
        }

        //Update Element.
        $Element->Size      = File::Size($TargetDirectory . $Element->File);
        $Element->Thumbnail = Thumbnail::Create($TargetDirectory . $Element->File);
        $Element->Save();
        return $Element;
    }

    /**
     * Recursively deletes a set of Elements and their children.
     * Triggers the {@link \vDesk\Archive\Element\Deleted}-Event for each deleted Element.
     *
     * @param int[]|null $Elements The IDs of the Elements to delete.
     *
     * @return \vDesk\Archive\Element[] An Array containing every deleted Element.
     */
    public static function DeleteElements(array $Elements = null): array {
        $DeletedElements = new Elements();

        $GetChildren = static function(Element $Element) use (&$GetChildren): \Generator {
            if(!$Element->AccessControlList->Delete) {
                return;
            }
            foreach($Element->Children as $Child) {
                if($Child->Type === Element::Folder) {
                    yield from $GetChildren($Child);
                }
                yield $Child;
            }
        };

        $Path = Settings::$Local["Archive"]["Directory"] . Path::Separator;

        //Loop through Elements and check for children.
        foreach($Elements ?? Command::$Parameters["Elements"] as $ID) {
            $Element = (new Element($ID))->Fill();
            if($Element->ID <= self::System || !$Element->AccessControlList->Delete) {
                break;
            }
            foreach($GetChildren($Element) as $Child) {
                if($Child->Type === Element::File) {
                    File::Delete($Path . $Child->File);
                }
                $Child->Delete();
                (new Deleted($Child))->Dispatch();
            }
            if($Element->Type === Element::File) {
                File::Delete($Path . $Element->File);
            }
            $Element->Delete();
            (new Deleted($Element))->Dispatch();
            $DeletedElements->Add($Element);
        }

        return $DeletedElements->ToDataView(true);
    }

    /**
     * Searches the archive for Elements with a similar name.
     *
     * @param string      $Value The name to search for.
     *
     * @param string|null $Filter
     *
     * @return \vDesk\Search\Results A Collection of found Elements with a similar name to the search value.
     */
    public static function Search(string $Value, string $Filter = null): Results {
        $Results = new Results();
        foreach(
            Expression::Select(
                "ID",
                "Name",
                "Extension",
                "Type",
                "AccessControlList"
            )
                      ->From("Archive.Elements")
                      ->Where(["Name" => ["LIKE" => "%{$Value}%"]])
            as
            $Element
        ) {
            if((new AccessControlList([], (int)$Element["AccessControlList"]))->Read) {
                $Results->Add(
                    new Result(
                        $Element["Name"],
                        "Element",
                        [
                            "ID"        => (int)$Element["ID"],
                            "Type"      => (int)$Element["Type"],
                            "Extension" => $Element["Extension"]
                        ]
                    )
                );
            }
        }
        return $Results;
    }

    /** @inheritDoc */
    public static function Status(): ?array {
        return [
            "FileCount"   => Expression::Select(Functions::Count("*"))
                                       ->From("Archive.Elements")
                                       ->Where(["Type" => Element::File])(),
            "FolderCount" => Expression::Select(Functions::Count("*"))
                                       ->From("Archive.Elements")
                                       ->Where(["Type" => Element::Folder])(),
            "DiskUsage"   => \round(
                                 (int)Expression::Select(Functions::Sum("Size"))
                                                ->From("Archive.Elements")() / 1000 / 1000, 2
                             ) . "MB"
        ];
    }
}
