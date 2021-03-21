<?php
declare(strict_types=1);

namespace Modules;

use Pages\vDesk\Contact;
use Pages\vDesk\Index;
use vDesk\Configuration\Settings;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\FileInfo;
use vDesk\IO\IOException;
use vDesk\IO\Path;
use vDesk\Modules\Module;
use vDesk\Pages\Page;
use vDesk\Pages\Request;
use vDesk\Utils\Log;

/**
 * Module for serving the tutorial files of the Package.
 *
 * @package Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class vDesk extends Module {
    
    /**
     * The index function of the Module.
     *
     * @return \Pages\vDesk A Page that represents the current overview of available tutorials.
     */
    public static function Index(): \Pages\vDesk {
        return new \Pages\vDesk(
            Pages: static::GetPages(),
            Content: new Index(Previews: static::GetPreviews())
        );
    }
    
    /**
     * Gets the currently installed Pages.
     *
     * @return array
     */
    public static function GetPages(): array {
        return (new DirectoryInfo(Settings::$Local["Pages"]["Pages"] . Path::Separator . "vDesk"))
            ->GetFiles()
            ->Map(static fn(FileInfo $Page): string => "\\Pages\\vDesk\\{$Page->Name}")
            ->Map(static fn(string $Page): Page => new $Page())
            ->ToArray();
    }
    
    /**
     * Gets the current available Package preview images.
     *
     * @return string[] An array containing the names of the available Package preview images.
     */
    public static function GetPreviews(): array {
        return (new DirectoryInfo(\Server . Path::Separator . "Images" . Path::Separator . "Packages"))
            ->GetFiles()
            ->Map(static fn(FileInfo $Image): string => "{$Image->Name}.{$Image->Extension}")
            ->ToArray();
    }
    
    /**
     * Sends the contents of the contact-form to a specified recipient email-address.
     *
     * @param null|string $Name    The name of the sender.
     * @param null|string $Topic   The topic of the message.
     * @param null|string $Email   The e-mail address for replies.
     * @param null|string $Message The message to send.
     *
     * @return string A page representing the transmission of the message.
     */
    public static function Send(?string $Name = null, ?string $Topic = null, ?string $Email = null, ?string $Message = null): string {
        $Name    ??= Request::$Parameters["Name"];
        $Topic   ??= Request::$Parameters["Topic"];
        $Email   ??= Request::$Parameters["Email"];
        $Message ??= Request::$Parameters["Message"];
        
        $Content = <<<Content
Name: $Name,
Topic: $Topic,
Email: $Email,
Message
_________
$Message
Content;
        try {
            if(\mail(Settings::$Local["Homepage"]["Recipient"], $Topic, $Content, ["From" => "contact@vdesk.cloud"])) {
                return "Thank you four message!";
            }
        } catch(\Throwable $Error) {
            Log::Error(__METHOD__, $Error->getMessage());
        }
        return "Sorry! Couldn't transmit your message due to technical issues! Try sending an E-Mail instead?";
    }
    
    /**
     * Displays a specified Page.
     *
     * @param string|null $Page The Page to display.
     *
     * @return \Pages\vDesk The requested tutorial.
     */
    public static function Page(string $Page = null): \Pages\vDesk {
        $Page  ??= Request::$Parameters["Page"];
        $Class = "\\Pages\\vDesk\\{$Page}";
        return new \Pages\vDesk(
            Pages: static::GetPages(),
            Content: new $Class()
        );
    }
    
}