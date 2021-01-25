<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Modules\Module;
use vDesk\Pages\Page;

/**
 * Class Pages
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Pages extends Module {
    
    public static function Index(): Page {
        return new Page(
            [
                "Message" => "Greetings",
                "Title"   => "Pages",
                "Author"  => "DevelopmentHero@gmail.com"
            ],
            ["Pages"]
        );
    }
    
}