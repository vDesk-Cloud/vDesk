<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\IO\Directory;
use vDesk\Locale\IPackage;

/**
 * vDesk base Package class.
 *
 * @package vDesk\Packages\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class DataProvider extends Package implements IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "DataProvider";

    /**
     * The version of the Package.
     */
    public const Version = "1.0.0";

    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The name of the Package.
     */
    public const Description = "Package providing a database abstraction layer.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Lib => [
                "vDesk/DataProvider.php",
                "vDesk/DataProvider"
            ]
        ]
    ];

    /**
     * Enumeration of available data providers.
     */
    public const Available = [
        "mysql" => "MySQL",
        "pgsql" => "PgSQL"
    ];

    /**
     * @inheritDoc
     */
    public static function PreInstall(\Phar $Phar, string $Path): void {

        $Options = \getopt("", ["Provider", "Server", "Port", "User", "Password"]);
        //Perform silent install if necessary parameters exist.
        if(isset($Options["Provider"], $Options["User"], $Options["Password"])) {
            $Provider = \strtolower($Options["Provider"]);
            if(!isset(self::Available[$Provider])) {
                throw new \InvalidArgumentException("Provider \"{$Options["Provider"]}\" is not supported. Current supported Drivers: " . \implode(", ", self::Available));
            }
            $Provider                        = self::Available[$Provider];
            Settings::$Local["DataProvider"] = new Settings\Local\Settings(
                [
                    "Provider" => $Provider,
                    "Server"   => $Options["Server"] ?? match ($Provider) {
                            self::Available["mysql"] => "p:localhost",
                            self::Available["pgsql"] => "localhost"
                        },
                    "Port"     => (int)($Options["Port"] ?? match ($Provider) {
                            self::Available["mysql"] => 3306,
                            self::Available["pgsql"] => 5432
                        }),
                    "User"     => $Options["User"],
                    "Password" => $Options["Password"]
                ],
                "DataProvider"
            );

        } else {
            $Provider = \readline("SQL provider [available = " . \implode(", ", self::Available) . "] [default = mysql]: ");
            if(!isset(self::Available[$Provider])) {
                throw new \InvalidArgumentException("Provider \"{$Provider}\" is not supported. Current supported Drivers: " . \implode(", ", self::Available));
            }
            $Provider = self::Available[$Provider];
            [$DefaultServer, $DefaultPort] = match ($Provider) {
                self::Available["mysql"] => ["p:localhost", 3306],
                self::Available["pgsql"] => ["localhost", 5432]
            };

            $Server                          = \readline("SQL server [default = {$DefaultServer}]: ");
            $Port                            = \readline("Port [default = {$DefaultPort}]: ");
            $User                            = \readline("SQL user: ");
            $Password                        = \readline("SQL user password: ");
            Settings::$Local["DataProvider"] = new Settings\Local\Settings(
                [
                    "Provider" => $Provider,
                    "Server"   => $Server !== "" ? $Server : $DefaultServer,
                    "Port"     => $Port !== "" ? (int)$Port : $Port,
                    "User"     => $User,
                    "Password" => $Password
                ],
                "DataProvider"
            );
        }

    }

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {

        //Setup DataProvider.
        new \vDesk\DataProvider(
            Settings::$Local["DataProvider"]["Provider"],
            Settings::$Local["DataProvider"]["Server"],
            Settings::$Local["DataProvider"]["Port"],
            Settings::$Local["DataProvider"]["User"],
            Settings::$Local["DataProvider"]["Password"]
        );

        //Extract files.
        self::Deploy($Phar, $Path);

    }


    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        //Delete files.
        Directory::Delete($Path, true);
    }

}