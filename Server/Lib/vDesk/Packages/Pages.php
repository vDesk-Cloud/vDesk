<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\IO\Directory;
use vDesk\IO\FileNotFoundException;
use vDesk\IO\Path;
use vDesk\Modules\UnknownModuleException;
use vDesk\Pages\IPackage;

/**
 * Pages Package manifest.
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Pages extends Package implements IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "Pages";

    /**
     * The version of the Package.
     */
    public const Version = "1.2.0";

    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The description of the Package.
     */
    public const Description = "Package that provides a simple MVC-framework.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Configuration" => "1.1.0"];

    /**
     * The license of the Package.
     */
    public const License = "MIT License";

    /**
     * The license text of the Package.
     */
    public const LicenseText = <<<LicenseText
Copyright 2020 Kerry Holz <DevelopmentHero@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
LicenseText;

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            "Pages.php",
            ".htaccess",
            self::Lib         => [
                "Pages.php",
                "vDesk/Pages"
            ],
            self::Modules     => [
                "Pages.php",
                "Error.php"
            ],
            self::Pages       => [
                "Pages.php"
            ],
            self::Templates   => [
                "Pages.php",
                "Error.php",
                "NotFound.php"
            ],
            self::Functions   => [
                "Template.php",
                "URL.php",
                "Stylesheet.php",
                "Script.php",
                "Image.php"
            ],
            self::Stylesheets => [
                "Pages.css"
            ],
            self::Images      => [
                "favicon.ico",
                "error.ico"
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {

        //Create Package configuration.
        Settings::$Local["Pages"] = new Settings\Local\Settings(
            [
                "Pages"       => (Directory::Create($Path . Path::Separator . self::Server . Path::Separator . self::Pages))->Path,
                "Templates"   => (Directory::Create($Path . Path::Separator . self::Server . Path::Separator . self::Templates))->Path,
                "Functions"   => (Directory::Create($Path . Path::Separator . self::Server . Path::Separator . self::Functions))->Path,
                "Scripts"     => (Directory::Create($Path . Path::Separator . self::Server . Path::Separator . self::Scripts))->Path,
                "Stylesheets" => (Directory::Create($Path . Path::Separator . self::Server . Path::Separator . self::Stylesheets))->Path,
                "Images"      => (Directory::Create($Path . Path::Separator . self::Server . Path::Separator . self::Images))->Path,
                "Cache"       => (Directory::Create($Path . Path::Separator . self::Server . Path::Separator . self::Cache))->Path,
                "ShowErrors"  => true
            ],
            "Pages"
        );

        //Create configuration for routes.
        Settings::$Local["Routes"] = new Settings\Local\Settings([
            "Default" => [
                "Module"  => "Pages",
                "Command" => "Index"
            ]
        ],
            "Routes"
        );

        //Create configuration for ErrorHandlers.
        Settings::$Local["ErrorHandlers"] = new Settings\Local\Settings([
            //Default ErrorHandler.
            "Default"                     => [
                "Module"  => "Error",
                "Command" => "Index"
            ],
            UnknownModuleException::class => [
                "Module"  => "Error",
                "Command" => "NotFound"
            ],
            FileNotFoundException::class  => [
                "Module"  => "Error",
                "Command" => "NotFound"
            ]
        ],
            "ErrorHandlers"
        );

        //Extract files.
        self::Deploy($Phar, $Path);

    }

    /** @inheritDoc */
    public static function Uninstall(string $Path): void {

        //Delete files.
        self::Undeploy();

        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . self::Pages, true);
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . self::Templates, true);
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . self::Functions, true);
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . self::Scripts, true);
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . self::Stylesheets, true);
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . self::Images, true);
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . self::Cache, true);

    }

}