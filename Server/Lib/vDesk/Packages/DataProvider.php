<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MySQL;
use vDesk\DataProvider\PgSQL;
use vDesk\DataProvider\MsSQL;
use vDesk\Environment\API;
use vDesk\IO\Directory;
use vDesk\Struct\Text;

/**
 * vDesk base Package class.
 *
 * @package vDesk\Packages\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class DataProvider extends Package {

    /**
     * The name of the Package.
     */
    public const Name = "DataProvider";

    /**
     * The version of the Package.
     */
    public const Version = "1.0.1";

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
    public const Dependencies = ["vDesk" => "1.1.0"];

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
        "pgsql" => "PgSQL",
        "mssql" => "MsSQL"
    ];

    /**
     * Enumeration of available CLI parameters.
     */
    private const Parameters = [
        "DataProvider.Provider", "DataProvider.Server", "DataProvider.Port", "DataProvider.User", "DataProvider.Password", "DataProvider.Database", "DataProvider.Persistent",
        "dp.pr", "dp.srv", "dp.port", "dp.u", "dp.pw", "dp.db", "dp.p"
    ];

    /**
     * @inheritDoc
     */
    public static function PreInstall(\Phar $Phar, string $Path): void {

        //Skip on web install.
        if(API::$Current !== API::CLI) {
            return;
        }

        //Determine default DataProvider.
        $DefaultProvider = null;
        if(\extension_loaded("pgsql")) {
            $DefaultProvider = "pgsql";
        }
        if(\extension_loaded("sqlsrv")) {
            $DefaultProvider = "mssql";
        }
        if(\extension_loaded("mysqli")) {
            $DefaultProvider = "mysql";
        }
        if($DefaultProvider === null) {
            throw new \RuntimeException("No supported database driver extensions found!");
        }

        $Parameters = \getopt("", self::Parameters);

        //Get DataProvider.
        $Provider = $Parameters["DataProvider.Provider"]
                    ?? $Parameters["dp.pr"]
                       ?? \readline("SQL DataProvider [available = " . \implode(", ", self::Available) . "] [default = {$DefaultProvider}]: ");
        $Provider = Text::IsNullOrWhitespace($Provider) ? $DefaultProvider : \strtolower($Provider);
        if(!isset(self::Available[$Provider])) {
            throw new \InvalidArgumentException("DataProvider \"$Provider\" is not supported. Current supported DataProviders: " . \implode(", ", self::Available));
        }
        $Provider = self::Available[$Provider];

        //Get server address.
        $Server = $Parameters["DataProvider.Server"] ?? $Parameters["dp.srv"] ?? \readline("SQL server [default = localhost]: ");
        $Server = Text::IsNullOrWhitespace($Server) ? "localhost" : $Server;

        //Get server port.
        $DefaultPort = match ($Provider) {
            self::Available["mysql"] => MySQL\Provider::Port,
            self::Available["pgsql"] => PgSQL\Provider::Port,
            self::Available["mssql"] => MsSQL\Provider::Port
        };
        $Port        = $Parameters["DataProvider.Server"] ?? $Parameters["dp.srv"] ?? \readline("Port [default = {$DefaultPort}]: ");
        $Port        = (int)$Port === 0 ? $DefaultPort : (int)$Port;

        //Get credentials.
        $User     = $Parameters["DataProvider.User"] ?? $Parameters["dp.u"] ?? \readline("SQL user: ");
        $Password = $Parameters["DataProvider.Password"] ?? $Parameters["dp.pw"] ?? \readline("SQL user password: ");

        //Get database for non MySQL servers.
        $Database = "vDesk";
        if($Provider !== "mysql") {
            $Database = $Parameters["DataProvider.Database"] ?? $Parameters["dp.db"] ?? \readline("Database [default = vDesk]: ");
            $Database = Text::IsNullOrWhitespace($Database) ? "vDesk" : $Database;
        }

        //Check whether to use persistent connections.
        $Persistent = isset($Parameters["DataProvider.Persistent"])
                      || isset($Parameters["dp.p"])
                      || \strtolower(\readline("Use persistent connections? (only recommended for smaller servers)[y/n][default = n]: ")) === "y";

        //Setup DataProvider/test connection.
        new \vDesk\DataProvider($Provider, $Server, $Port, $User, $Password, null, $Persistent);

        Settings::$Local["DataProvider"] = new Settings\Local\Settings(
            [
                "Provider"   => $Provider,
                "Server"     => $Server,
                "Port"       => $Port,
                "User"       => $User,
                "Password"   => $Password,
                "Persistent" => $Persistent,
                "Database"   => $Database
            ],
            "DataProvider"
        );

    }

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {

        //Setup database for non MySQL Providers.
        if(Settings::$Local["DataProvider"]["Provider"] !== self::Available["mysql"]) {
            Expression::Create()
                      ->Database(Settings::$Local["DataProvider"]["Database"])
                      ->Execute();

            //Reconnect to database.
            new \vDesk\DataProvider(
                Settings::$Local["DataProvider"]["Provider"],
                Settings::$Local["DataProvider"]["Server"],
                Settings::$Local["DataProvider"]["Port"],
                Settings::$Local["DataProvider"]["User"],
                Settings::$Local["DataProvider"]["Password"],
                Settings::$Local["DataProvider"]["Database"],
                Settings::$Local["DataProvider"]["Persistent"]
            );
        }

        //Extract files.
        self::Deploy($Phar, $Path);

    }

    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {

        //Delete database of non MySQL Providers.
        if(Settings::$Local["DataProvider"]["Provider"] !== self::Available["mysql"]) {
            Expression::Drop()
                      ->Database(Settings::$Local["DataProvider"]["Database"])
                      ->Execute();
        }

        //Delete files.
        Directory::Delete($Path, true);
    }

}