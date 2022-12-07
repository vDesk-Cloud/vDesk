<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\DataProvider\Expression;
use vDesk\Packages\Package;

/**
 * Security Update manifest class.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Security extends Update {

    /**
     * The class name of the Package of the Update.
     */
    public const Package = \vDesk\Packages\Security::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.1.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Simplified loading of users and groups.
- Fixed MembershipEditor not displaying memberships when user is selected.
- Implemented automatic enabling of editor-controls when the "new user/group"-items have been selected.
- Membership Groups will be now filled additionally with their name. 
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Security/GroupList.js",
                    "vDesk/Security/Group/Administration.js",
                    "vDesk/Security/Group/Editor.js",
                    "vDesk/Security/UserList.js",
                    "vDesk/Security/User/Administration.js",
                    "vDesk/Security/User/Editor.js",
                    "vDesk/Security/User/MembershipEditor.js"
                ]
            ],
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Security/Group.php",
                    "vDesk/Security/Group/Collection.php",
                    "vDesk/Security/User.php",
                    "vDesk/Security/User/Collection.php",
                    "vDesk/Security/User/Groups.php"
                ],
                Package::Modules => [
                    "Security.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/Security/Groups.php",
                    "vDesk/Security/GroupsView.php",
                    "vDesk/Security/Users.php",
                    "vDesk/Security/UsersView.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);

        Expression::Drop()
                  ->Index("UserName")
                  ->On("Security.Users")
                  ->Execute();
        Expression::Create()
                  ->Index("UserName", true)
                  ->On("Security.Users", ["Name" => 255])
                  ->Execute();
        Expression::Create()
                  ->Index("UserEmail", true)
                  ->On("Security.Users", ["Email" => 255])
                  ->Execute();
    }
}