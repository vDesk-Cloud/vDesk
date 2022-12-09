<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Archive Update manifest class.
 *
 * @package vDesk\Archive
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Archive extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Archive::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.1.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Solved issue caused by Firefox-bugg firing "dragenter"-event twice on elements.
- Restricted visual dragover-effects to folder elements only.
- Removed "Background"-CSS-class from client TreeView-control.
- Unified parameters of internal server module API.
- Closed potential security issues due to missing permission checks on file updates, fetching metadata and folder children.
- Removed unused "GetAttributes"-command.
- Made names of elements unique per folder.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Archive/TreeView.js",
                    "vDesk/Archive/Element.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "Archive.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}