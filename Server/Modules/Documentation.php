<?php
declare(strict_types=1);

namespace Modules;

use Pages\Documentation\Client;
use Pages\Documentation\Index;
use Pages\Documentation\Server;
use Pages\Documentation\Packages;
use vDesk\Configuration\Settings;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\Modules\Module;
use vDesk\Pages\Page;
use vDesk\Pages\Request;

/**
 * Module for serving the Documentation files of the Package.
 *
 * @package Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Documentation extends Module {

    /**
     * The entry point of the Documentation Page.
     *
     * @return \Pages\Documentation The index Page of the Documentation Package.
     */
    public static function Index(): \Pages\Documentation {
        return new \Pages\Documentation(
            Pages: static::GetPages(),
            Content: new \Pages\Documentation\Index(Pages: static::GetPages(), Packages: static::Packages())
        );
    }

    /**
     * Gets the currently installed Documentation Pages.
     *
     * @return array
     */
    public static function GetPages(): array {
        return (new DirectoryInfo(Settings::$Local["Pages"]["Pages"] . Path::Separator . "Documentation"))
            ->GetFiles()
            ->Map(static fn(FileInfo $Page): string => "\\Pages\\Documentation\\{$Page->Name}")
            ->Map(static fn(string $Page): Page => new $Page())
            ->ToArray();
    }


    /**
     * Displays a specified Page.
     *
     * @param string|null $Topic The Page to display.
     *
     * @return \Pages\Documentation The requested Page.
     */
    public static function Topic(string $Topic = null): Page {
        $Topic ??= Request::$Parameters["Topic"];
        $Class = "\\Pages\\Documentation\\{$Topic}";
        return new \Pages\Documentation(
            Pages: static::GetPages(),
            Content: new $Class()
        );
    }

    /**
     * Gets the currently installed Client Documentation Pages.
     *
     * @return array
     */
    public static function ClientPages(): array {
        return (new DirectoryInfo(Settings::$Local["Pages"]["Pages"] . Path::Separator . "Documentation" . Path::Separator . "Client"))
            ->GetFiles()
            ->Map(static fn(FileInfo $Topic): string => "\\Pages\\Documentation\\Client\\{$Topic->Name}")
            ->Map(static fn(string $Topic): Page => new $Topic())
            ->ToArray();
    }

    /**
     * Displays a specified Client Documentation Pages.
     *
     * @param string|null $Topic The Client Documentation Page to display.
     *
     * @return \Pages\Documentation\Client\Page The requested Client Documentation Page.
     */
    public static function ClientPage(string $Topic = null): Client\Page {
        $Topic ??= Request::$Parameters["Topic"];
        $Class = "\\Pages\\Documentation\\Client\\{$Topic}";
        return new Client\Page(
            Pages: static::GetPages(),
            Topics: static::ClientPages(),
            Topic: new $Class()
        );
    }

    /**
     * Gets the currently installed Server Documentation Pages.
     *
     * @return array
     */
    public static function ServerPages(): array {
        return (new DirectoryInfo(Settings::$Local["Pages"]["Pages"] . Path::Separator . "Documentation" . Path::Separator . "Server"))
            ->GetFiles()
            ->Map(static fn(FileInfo $Topic): string => "\\Pages\\Documentation\\Server\\{$Topic->Name}")
            ->Map(static fn(string $Topic): Page => new $Topic())
            ->ToArray();
    }

    /**
     * Displays a specified Server Documentation Pages.
     *
     * @param string|null $Topic The Server Documentation Page to display.
     *
     * @return \Pages\Documentation\Server\Page The requested Server Documentation Page.
     */
    public static function ServerPage(string $Topic = null): Server\Page {
        $Topic ??= Request::$Parameters["Topic"];
        $Class = "\\Pages\\Documentation\\Server\\{$Topic}";
        return new Server\Page(
            Pages: static::GetPages(),
            Topics: static::ServerPages(),
            Topic: new $Class()
        );
    }

    /**
     * Gets the currently installed Package Documentation Pages.
     *
     * @return array
     */
    public static function Packages(): array {
        return (new DirectoryInfo(Settings::$Local["Pages"]["Pages"] . Path::Separator . "Documentation" . Path::Separator . "Packages"))
            ->GetFiles()
            ->Map(static fn(FileInfo $Package): string => "\\Pages\\Documentation\\Packages\\{$Package->Name}")
            ->Map(static fn(string $Package): Page => new $Package())
            ->ToArray();
    }

    /**
     * Displays a specified Packages Documentation Page.
     *
     * @param string|null $Package The Package Documentation Page to display.
     *
     * @return \Pages\Documentation\Packages\Page The requested Packages Documentation Page.
     */
    public static function Package(string $Package = null): Packages\Page {
        $Package ??= Request::$Parameters["Package"];
        $Class   = "\\Pages\\Documentation\\Packages\\{$Package}";
        return new Packages\Page(
            Pages: static::GetPages(),
            Packages: static::Packages(),
            Package: new $Class()
        );
    }

}