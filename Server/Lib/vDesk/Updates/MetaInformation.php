<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Modules;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Packages\Package;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Type;

/**
 * MetaInformation Update manifest class.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class MetaInformation extends Update {
    
    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\MetaInformation::class;
    
    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.0";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed alignment of validator constraints in mask designer.
- Changed Max constraint of numeric validators to JavaScript's max safe integer.
- Changed property access operator in module to array indexer.
- Implemented auto conversion to \DateTime instances for dataset rows.
- Implemented missing search command.
- Fixed changing of masks of existing datasets.
Description;
    
    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Design => [
                    "vDesk/MetaInformation/Mask/Row/Validator/Number.css",
                    "vDesk/MetaInformation/Mask/Row/Validator/Money.css"
                ],
                Package::Lib    => [
                    "vDesk/MetaInformation/DataSet.js",
                    "vDesk/MetaInformation/DataSet/Editor.js",
                    "vDesk/MetaInformation/Mask/Editor.js",
                    "vDesk/MetaInformation/Mask/Row/Editor.js",
                    "vDesk/MetaInformation/Mask/Row/Validator/Number.js",
                    "vDesk/MetaInformation/Mask/Row/Validator/Money.js",
                    "vDesk/MetaInformation/MaskDesigner.js",
                    "vDesk/MetaInformation/MaskList.js",
                    "vDesk/MetaInformation/Masks.js",
                    "vDesk/MetaInformation/Attributes.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "MetaInformation.php"
                ],
                Package::Lib     => [
                    "vDesk/MetaInformation/DataSet.php",
                    "vDesk/MetaInformation/DataSet/Row.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Design => [
                    "vDesk/MetaInformation/Mask/Row/Validator/Number.css",
                    "vDesk/MetaInformation/Mask/Row/Validator/Money.css"
                ],
                Package::Lib    => [
                    "vDesk/MetaInformation/DataSet.js",
                    "vDesk/MetaInformation/DataSet/Editor.js",
                    "vDesk/MetaInformation/Mask/Editor.js",
                    "vDesk/MetaInformation/Mask/Row/Editor.js",
                    "vDesk/MetaInformation/Mask/Row/Validator/Number.js",
                    "vDesk/MetaInformation/Mask/Row/Validator/Money.js",
                    "vDesk/MetaInformation/MaskDesigner.js",
                    "vDesk/MetaInformation/MaskList.js",
                    "vDesk/MetaInformation/Masks.js",
                    "vDesk/MetaInformation/Attributes.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "MetaInformation.php"
                ],
                Package::Lib     => [
                    "vDesk/MetaInformation/DataSet.php",
                    "vDesk/MetaInformation/DataSet/Row.php"
                ]
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        //Install missing Search Command.
        \vDesk::$Phar = false;
        $Search       = new Command(
            null,
            Modules::MetaInformation(),
            "Search",
            true,
            false,
            null,
            new Collection([
                new Parameter(null, null, "ID", Type::Int, false, false),
                new Parameter(null, null, "Values", Type::Array, false, false),
                new Parameter(null, null, "All", Type::Bool, false, false),
                new Parameter(null, null, "Strict", Type::Bool, false, false)
            ])
        );
        $Search->Save();
        \vDesk::$Phar = true;
        
        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);
        
    }
}