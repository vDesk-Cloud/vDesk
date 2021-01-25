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
     * Gets an Element.
     *
     * @param null|int $ID The ID of the Element to get.
     *
     * @return \vDesk\Archive\Element The Element to get.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have read permissions on the Element to get.
     */
    public static function GetElement(int $ID = null): Element {
        $Element = (new Element($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Element->AccessControlList->Read) {
            throw new UnauthorizedAccessException();
        }
        return $Element;
    }
    
    /**
     * Returns all child Elements of a specified folder Element.
     *
     * @param null|\vDesk\Archive\Element $Parent The parent folder Element to get the child Elements of.
     *
     * @return \vDesk\Archive\Elements A Collection of all child Elements of the specified folder Element off which the current User has read
     *                                            permissions.
     */
    public static function GetElements(Element $Parent = null): Elements {
        $Parent   ??= new Element(Command::$Parameters["ID"]);
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
     * Returns a branch of IDs from a specified destination Element starting from the Archive root Element.
     *
     * @param null|\vDesk\Archive\Element $Element The destination Element to get its history branch of.
     *
     * @return int[] IDs of the logical branch.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read the target Element.
     */
    public static function GetBranch(Element $Element = null): array {
        $Element ??= (new Element(Command::$Parameters["ID"]))->Fill();
        if(!$Element->AccessControlList->Read) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to fetch branch of Element without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Elements = [$Element->ID];
        do {
            //Check if user can view the Element.
            if($Element->Parent->AccessControlList->Read) {
                $Elements[] = $Element->Parent->ID;
                $Element    = $Element->Parent;
            } else {
                throw new UnauthorizedAccessException();
            }
        } while($Element->ID > self::Root);
        return \array_reverse($Elements);
    }
    
    /**
     * Uploads a file to the Archive.
     * Triggers the {@link \vDesk\Archive\Element\Created}-Event for each uploaded file Element.
     *
     * @param \vDesk\Archive\Element|null $Parent The target folder Element.
     * @param string|null                 $Name   The name of the file to upload.
     * @param \vDesk\IO\FileInfo|null     $File   The file to upload.
     *
     * @return \vDesk\Archive\Element The uploaded Element.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write permissions on the target folder Element.
     */
    public static function Upload(Element $Parent = null, string $Name = null, FileInfo $File = null): Element {
        
        $Parent ??= new Element(Command::$Parameters["Parent"]);
        $Name   ??= Command::$Parameters["Name"];
        $File   ??= Command::$Parameters["File"];
        
        //Check if the User can write to the directory.
        if(!$Parent->AccessControlList->Write) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to upload a file without having permissions.");
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
            $TargetFile->Write($TempFile->Read());
        }
        $TargetFile->Close();
        $TempFile->Close();
        
        //Create a new Element for the uploaded file.
        $Element = new Element(
            null,
            \vDesk::$User,
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
        
        //Save new Element.
        $Element->Save();
        (new Created($Element))->Dispatch();
        Log::Info(__METHOD__, \vDesk::$User->Name . " uploaded [{$Element->ID}]({$Element->Name}) to [{$Parent->ID}]({$Parent->Name})");
        return $Element;
    }
    
    /**
     * Downloads the file of a specified Element.
     *
     * @param \vDesk\Archive\Element|null $Element The Element to download the file of.
     *
     * @return \vDesk\IO\FileInfo A FileInfo that represents the file of the specified Element to download.
     * @throws \vDesk\Struct\InvalidOperationException Thrown if the Element to download is not a file.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have read permissions on the Element to download.
     */
    public static function Download(Element $Element = null): FileInfo {
        $Element ??= (new Element(Command::$Parameters["ID"]))->Fill();
        if(!$Element->AccessControlList->Read) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to download a file Element without having permissions.");
            throw new UnauthorizedAccessException();
        }
        if($Element->Type !== Element::File) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to download a folder Element.");
            throw new InvalidOperationException();
        }
        //Send file.
        $File           = new FileInfo(Settings::$Local["Archive"]["Directory"] . Path::Separator . $Element->File);
        $File->MimeType = Expression::Select("MimeType")
                                    ->From("Archive.MimeTypes")
                                    ->Where(["Extension" => $Element->Extension])
                                    ->Execute()
                                    ->ToValue();
        return $File;
    }
    
    /**
     * Gets the Attributes of an Element.
     *
     * @param null|\vDesk\Archive\Element $Element The Element to get the Attributes of..
     *
     * @return \vDesk\Archive\Element The speElement of the specified Element.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read Attributes.
     */
    public static function GetAttributes(Element $Element = null): Element {
        if(!\vDesk::$User->Permissions["ReadAttributes"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view Attributes without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return ($Element ?? new Element(Command::$Parameters["ID"]))->Fill();
    }
    
    /**
     * Creates a new folder Element.
     * Triggers the {@link \vDesk\Archive\Element\Created}-Event for the created folder Element.
     *
     * @param \vDesk\Archive\Element|null $Parent The parent Element of the folder Element to create.
     * @param string|null                 $Name   The name of the folder Element.
     *
     * @return \vDesk\Archive\Element The the newly created folder Element.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write permissions on the parent Element.
     *
     */
    public static function CreateFolder(Element $Parent = null, string $Name = null): Element {
        $Parent ??= new Element(Command::$Parameters["Parent"]);
        if(!$Parent->AccessControlList->Write) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to create a new folder Element without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Folder = new Element(
            null,
            \vDesk::$User,
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
        Log::Info(__METHOD__, \vDesk::$User->Name . " created new directory [{$Folder->ID}]({$Folder->Name}) in directory [{$Parent->ID}]({$Parent->Name})");
        (new Created($Folder))->Dispatch();
        return $Folder;
    }
    
    /**
     * Moves a set of Elements to a new destination folder Element.
     * Triggers the {@link \vDesk\Archive\Element\Moved}-Event for every moved Element.
     *
     * @param null|\vDesk\Archive\Element $Target   The target folder Element.
     * @param int[]|null                  $Elements The ID's of the Elements to move.
     *
     * @return boolean True on success.
     * @throws \vDesk\Security\UnauthorizedAccessException
     */
    public static function Move(Element $Target = null, array $Elements = null): bool {
        $Target ??= new Element(Command::$Parameters["Target"]);
        if(!$Target->AccessControlList->Write) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to move Elements without having permissions.");
            throw new UnauthorizedAccessException();
            
        }
        
        //Update the parent entry of the Elements.
        foreach($Elements ?? Command::$Parameters["Elements"] as $ID) {
            $Element = (new Element($ID))->Fill();
            if($Element->ID > self::System && $Element->AccessControlList->Write && $Element->ID !== $Target->ID) {
                $Element->Parent = $Target;
                $Element->Save();
                Log::Info(__METHOD__, \vDesk::$User->Name . " moved [{$Element->ID}]({$Element->Name}) to [{$Target->ID}]({$Target->Name}).");
                (new Moved($Element))->Dispatch();
            } else {
                continue;
            }
        }
        return true;
    }
    
    /**
     * Copies a set of Elements to a new destination folder Element.
     * Triggers the {@link \vDesk\Archive\Element\Created}-Event for each copied Element.
     *
     * @param null|\vDesk\Archive\Element $Target   The target folder Element to copy the specified Elements into.
     * @param int[]|null                  $Elements The IDs of the Elements to copy to the specified new parent Element.
     *
     * @return boolean True if the whole tree has been successfully copied.
     * @throws \vDesk\Security\UnauthorizedAccessException
     * @todo Return the list of new Elements.
     */
    public static function Copy(Element $Target = null, array $Elements = null): bool {
        $Target ??= new Element(Command::$Parameters["Target"]);
        //Check if the User can write to the target folder Element.
        if(!$Target->AccessControlList->Write) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to copy Elements to [{$Target->ID}]({$Target->Name}) without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        $RecursiveCopy = static function(Element $Target, Element $Element) use (&$RecursiveCopy) {
            
            if($Target->AccessControlList->Write) {
                
                //Copy values.
                $CopiedElement = new Element(
                    null,
                    \vDesk::$User,
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
                                \vDesk::$User->Name . " copied [{$ChildElement->ID}]({$ChildElement->Name}) to [{$CopiedElement->ID}]({$CopiedElement->Name})"
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
                Log::Info(__METHOD__, \vDesk::$User->Name . " moved [{$Element->ID}]({$Element->Name}) to [{$Target->ID}]({$Target->Name})");
            }
        }
        
        return true;
    }
    
    /**
     * Renames an Element.
     * Triggers the {@link \vDesk\Archive\Element\Renamed}-Event for the renamed Element.
     *
     * @param null|\vDesk\Archive\Element $Element The Element to rename.
     * @param null|string                 $Name    The new name of the Element.
     *
     * @return bool True if the Element has been successfully renamed.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write permissions on the Element to rename.
     */
    public static function Rename(Element $Element = null, string $Name = null): bool {
        $Element ??= new Element(Command::$Parameters["ID"]);
        $Element->Fill();
        if($Element->ID <= self::System || !$Element->AccessControlList->Write) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to rename Element without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Element->Name = $Name ?? Command::$Parameters["Name"];
        $Element->Save();
        Log::Info(__METHOD__, \vDesk::$User->Name . " renamed Element [{$Element->ID}] to '{$Element->Name}'");
        (new Renamed($Element))->Dispatch();
        return true;
    }
    
    /**
     * Updates the file of a specified Element.
     *
     * @param int|null                $ID   The ID of the Element to update its file of.
     * @param \vDesk\IO\FileInfo|null $File The new file of the Element.
     *
     * @return \vDesk\Archive\Element The updated Element.
     */
    public static function UpdateFile(int $ID = null, FileInfo $File = null): Element {
        $Element         = (new Element($ID ?? Command::$Parameters["ID"]))->Fill();
        $TargetDirectory = Settings::$Local["Archive"]["Directory"] . Path::Separator;
        
        //Overwrite file.
        $TargetFile = new FileStream($TargetDirectory . $Element->File, Mode::Truncate | Mode::Binary);
        $TempFile   = ($File ?? Command::$Parameters["File"])->Open();
        while(!$TempFile->EndOfStream()) {
            $TargetFile->Write($TempFile->Read());
        }
        $TargetFile->Close();
        $TempFile->Close();
        
        //Update Element.
        $Element->Size      = File::Size($TargetDirectory . $Element->File);
        $Element->Thumbnail = Thumbnail::Create($TargetDirectory . $Element->File);
        $Element->Save();
        return $Element;
    }
    
    /**
     * Deletes a set of Elements.
     * Recursively deletes any child Elements.
     * Triggers the {@link \vDesk\Archive\Element\Deleted}-Event for each deleted {@link \vDesk\Archive\Element}.
     *
     * @param int[]|null $Elements The IDs of the Elements to delete.
     *
     * @return \vDesk\Archive\Element[] The deleted Elements.
     */
    public static function DeleteElements(array $Elements = null): array {
        $DeletedElements = new Elements();
        
        /**
         * @param \vDesk\Archive\Element $Element
         *
         * @return \Generator
         */
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
     * @return \vDesk\Search\Results A Collection of found Elements with a similar name.
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
    
    /**
     * Gets the status information of the Archive.
     *
     * @return null|array An array containing the amount of files, folders and overall diskusage.
     */
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
