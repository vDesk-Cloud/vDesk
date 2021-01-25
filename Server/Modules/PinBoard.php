<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Archive\Element;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\Functions;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\PinBoard\Attachment;
use vDesk\PinBoard\Attachments;
use vDesk\PinBoard\Notes;
use vDesk\PinBoard\Note;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Utils\Log;

/**
 * Module for organizing notes and attachments which refer to existing {@link \vDesk\Archive\Element} objects.
 *
 * @package vDesk\PinBoard
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class PinBoard extends Module {
    
    /**
     * Fetches all Notes and Attachments of the current logged in User.
     *
     * @return array
     */
    public static function GetEntries(): array {
        return [
            "Attachments" => Attachments::FromOwner(\vDesk::$User)->ToDataView(),
            "Notes"       => Notes::FromOwner(\vDesk::$User)->ToDataView()
        ];
    }
    
    /**
     * Creates a new Note.
     *
     * @param null|\vDesk\Security\User $Owner   The owner of the Note.
     * @param null|int                  $X       The horizontal position of the Note.
     * @param null|int                  $Y       The vertical position of the Note.
     * @param null|int                  $Width   The width of the Note.
     * @param null|int                  $Height  The height of the Note.
     * @param null|string               $Color   The color of the Note.
     * @param null|string               $Content The text content of the Note.
     *
     * @return int The ID of the newly created Note.
     */
    public static function CreateNote(
        User $Owner = null,
        int $X = null,
        int $Y = null,
        int $Width = null,
        int $Height = null,
        string $Color = null,
        string $Content = null
    ): int {
        $Note = new Note(
            null,
            $Owner ?? \vDesk::$User,
            $X ?? Command::$Parameters["X"],
            $Y ?? Command::$Parameters["Y"],
            $Width ?? Command::$Parameters["Width"],
            $Height ?? Command::$Parameters["Height"],
            $Color ?? Command::$Parameters["Color"],
            $Content ?? Command::$Parameters["Content"] ?? ""
        );
        $Note->Save();
        return $Note->ID;
    }
    
    /**
     * Updates a Note.
     *
     * @param null|int    $ID      The ID of the Note.
     * @param null|int    $X       The new horizontal position of the Note.
     * @param null|int    $Y       The new vertical position of the Note.
     * @param null|int    $Width   The new width of the Note.
     * @param null|int    $Height  The new height of the Note.
     * @param null|string $Color   The new color of the Note.
     * @param null|string $Content The new text content of the Note.
     *
     * @return \vDesk\PinBoard\Note The updated Note.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User is doesn't have ownership of the Note to update.
     */
    public static function UpdateNote(
        int $ID = null,
        int $X = null,
        int $Y = null,
        int $Width = null,
        int $Height = null,
        string $Color = null,
        string $Content = null
    ): Note {
        $Note = (new Note($ID ?? Command::$Parameters["ID"]))->Fill();
        if($Note->Owner->ID !== \vDesk::$User->ID) {
            Log::Error(__METHOD__, \vDesk::$User->Name . " tried to change position of Note [{$Note->ID}] without having ownership.");
            throw new UnauthorizedAccessException();
        }
        $Note->X       = $X ?? Command::$Parameters["X"];
        $Note->Y       = $Y ?? Command::$Parameters["Y"];
        $Note->Width   = $Width ?? Command::$Parameters["Width"];
        $Note->Height  = $Height ?? Command::$Parameters["Height"];
        $Note->Color   = $Color ?? Command::$Parameters["Color"];
        $Note->Content = $Content ?? Command::$Parameters["Content"];
        $Note->Save();
        return $Note;
    }
    
    /**
     * Updates the position of a Note.
     *
     * @param null|int $ID The ID of the Note to update.
     * @param null|int $X  The new horizontal position of the Note.
     * @param null|int $Y  The new vertical position of the Note.
     *
     * @return boolean True if the position of the Note has been successfully updated.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User is doesn't have ownership of the Note to update.
     */
    public static function UpdateNotePosition(int $ID = null, int $X = null, int $Y = null): bool {
        $Note = (new Note($ID ?? Command::$Parameters["ID"]))->Fill();
        if($Note->Owner->ID !== \vDesk::$User->ID) {
            Log::Error(__METHOD__, \vDesk::$User->Name . " tried to change position of Note [{$Note->ID}] without having ownership.");
            throw new UnauthorizedAccessException();
        }
        $Note->X = $X ?? Command::$Parameters["X"];
        $Note->Y = $Y ?? Command::$Parameters["Y"];
        $Note->Save();
        return true;
    }
    
    /**
     * Updates the size of a Note.
     *
     * @param null|int $ID     The ID of the Note to update.
     * @param null|int $Width  The new width of the Note.
     * @param null|int $Height The new height of the Note.
     *
     * @return boolean True if the size of the Note has been successfully updated.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User is doesn't have ownership of the Note to update.
     */
    public static function UpdateNoteSize(int $ID = null, int $Width = null, int $Height = null): bool {
        $Note = (new Note($ID ?? Command::$Parameters["ID"]))->Fill();
        if($Note->Owner->ID !== \vDesk::$User->ID) {
            Log::Error(__METHOD__, \vDesk::$User->Name . " tried to change size of Note [{$Note->ID}] without having ownership.");
            throw new UnauthorizedAccessException();
        }
        $Note->Width  = $Width ?? Command::$Parameters["Width"];
        $Note->Height = $Height ?? Command::$Parameters["Height"];
        $Note->Save();
        return true;
    }
    
    /**
     * Updates the color of a Note.
     *
     * @param null|int    $ID    The ID of the Note to update.
     * @param null|string $Color The new color of the Note.
     *
     * @return boolean True if the color of the Note has been successfully updated.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User is doesn't have ownership of the Note to update.
     */
    public static function UpdateNoteColor(int $ID = null, string $Color = null): bool {
        $Note = (new Note($ID ?? Command::$Parameters["ID"]))->Fill();
        if($Note->Owner->ID !== \vDesk::$User->ID) {
            Log::Error(__METHOD__, \vDesk::$User->Name . " tried to change color of Note [{$Note->ID}] without having ownership.");
            throw new UnauthorizedAccessException();
        }
        $Note->Color = $Color ?? Command::$Parameters["Color"];
        $Note->Save();
        return true;
    }
    
    /**
     * Updates the text content of a Note.
     *
     * @param null|int    $ID      The ID of the Note to update.
     * @param null|string $Content The new text content of the Note.
     *
     * @return boolean  True if the text content of the Note has been successfully updated.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User is doesn't have ownership of the Note to update.
     */
    public static function UpdateNoteContent(int $ID = null, string $Content = null): bool {
        $Note = (new Note($ID ?? Command::$Parameters["ID"]))->Fill();
        if($Note->Owner->ID !== \vDesk::$User->ID) {
            Log::Error(__METHOD__, \vDesk::$User->Name . " tried to change content of Note [{$Note->ID}] without having ownership.");
            throw new UnauthorizedAccessException();
        }
        $Note->Content = $Content ?? Command::$Parameters["Content"];
        $Note->Save();
        return true;
    }
    
    /**
     * Deletes a Note.
     *
     * @param null|int $ID The ID of the Note to delete.
     *
     * @return boolean True if the Note has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User is doesn't have ownership of the Note to delete.
     */
    public static function DeleteNote(int $ID = null): bool {
        $Note = new Note($ID ?? Command::$Parameters["ID"]);
        if($Note->Owner->ID !== \vDesk::$User->ID) {
            Log::Error(__METHOD__, \vDesk::$User->Name . " tried to delete Note [{$Note->ID}] without having ownership.");
            throw new UnauthorizedAccessException();
        }
        $Note->Delete();
        return true;
    }
    
    /**
     *  Creates a new Attachment.
     *
     * @param \vDesk\Security\User|null   $Owner   The owner of the Attachment.
     * @param int|null                    $X       The horizontal position of the Attachment.
     * @param int|null                    $Y       The vertical position of the Attachment.
     * @param \vDesk\Archive\Element|null $Element The attached Element of the Attachment.
     *
     *
     * @return int The ID of the newly created Attachment.
     */
    public static function CreateAttachment(User $Owner = null, int $X = null, int $Y = null, Element $Element = null): int {
        $Attachment = new Attachment(
            null,
            $Owner ?? \vDesk::$User,
            $X ?? Command::$Parameters["X"],
            $Y ?? Command::$Parameters["Y"],
            $Element ?? new Element(Command::$Parameters["Element"])
        );
        $Attachment->Save();
        return $Attachment->ID;
    }
    
    /**
     * Updates the position of an Attachment.
     *
     * @param null|int $ID The ID of the Attachment to update.
     * @param null|int $X  The new horizontal position of the Attachment.
     * @param null|int $Y  The new vertical position of the Attachment.
     *
     * @return boolean True if the position of the Attachment has been successfully updated.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User is doesn't have ownership of the Attachment to update.
     */
    public static function UpdateAttachmentPosition(int $ID = null, int $X = null, int $Y = null): bool {
        $Attachment = (new Attachment($ID ?? Command::$Parameters["ID"]))->Fill();
        if($Attachment->Owner->ID !== \vDesk::$User->ID) {
            Log::Error(__METHOD__, \vDesk::$User->Name . " tried to change position of Attachment [{$Attachment->ID}] without having ownership.");
            throw new UnauthorizedAccessException();
        }
        $Attachment->X = $X ?? Command::$Parameters["X"];
        $Attachment->Y = $Y ?? Command::$Parameters["Y"];
        $Attachment->Save();
        return true;
    }
    
    /**
     * Deletes an Attachment.
     *
     * @param null|int $ID The ID of the Attachment to delete.
     *
     * @return boolean True if the Attachment has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User is doesn't have ownership of the Attachment to delete.
     */
    public static function DeleteAttachment(int $ID = null): bool {
        $Attachment = new Attachment($ID ?? Command::$Parameters["ID"]);
        if($Attachment->Owner->ID !== \vDesk::$User->ID) {
            Log::Error(__METHOD__, \vDesk::$User->Name . " tried to delete Attachment [{$Attachment->ID}] without having ownership.");
            throw new UnauthorizedAccessException();
        }
        $Attachment->Delete();
        return true;
    }
    
    /**
     * Gets the status information of the PinBoard.
     *
     * @return null|array An array containing the amount of PinBoard Notes and attached Elements.
     */
    public static function Status(): ?array {
        return [
            "NoteCount"       => Expression::Select(Functions::Count("*"))
                                           ->From("PinBoard.Notes")(),
            "AttachmentCount" => Expression::Select(Functions::Count("*"))
                                           ->From("PinBoard.Attachments")()
        ];
    }
    
}