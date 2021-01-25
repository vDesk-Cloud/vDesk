<?php
declare(strict_types=1);

namespace Pages\vDesk;

use Pages\vDesk;

/**
 * Class About
 *
 * @package Pages\vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Contact extends vDesk {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Contact";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Contact";
    
    /**
     * Initializes a new instance of the Documentation Page.
     *
     * @param null|iterable $Values      Initializes the Documentation Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Documentation Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Documentation Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Documentation Page with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = [],
        ?iterable $Stylesheets = [
            "vDesk/Stylesheet",
            "../../Client/Design/vDesk",
            "../../Client/Design/vDesk/Controls/SuggestionTextBox",
            "../../Client/Design/vDesk/Controls/EditControl",
            "../../Client/Design/vDesk/Controls/EditControl/Text",
            "../../Client/Design/vDesk/Controls/EditControl/Suggest",
            "../../Client/Design/vDesk/Controls/GroupBox",
        ],
        ?iterable $Scripts = [
            "../../Client/Lib/vDesk",
            "../../Client/Lib/vDesk/Utils",
            "../../Client/Lib/vDesk/Utils/Expression",
            "../../Client/Lib/vDesk/Struct",
            "../../Client/Lib/vDesk/Struct/Type",
            "../../Client/Lib/vDesk/Struct/Extension/Type",
            "../../Client/Lib/vDesk/Struct/ObservableArray",
            "../../Client/Lib/vDesk/Events",
            "../../Client/Lib/vDesk/Events/BubblingEvent",
            "../../Client/Lib/vDesk/Visual",
            "../../Client/Lib/vDesk/Visual/Animation/Animation",
            "../../Client/Lib/vDesk/Controls",
            "../../Client/Lib/vDesk/Controls/GroupBox",
            "../../Client/Lib/vDesk/Controls/EditControl",
            "../../Client/Lib/vDesk/Controls/EditControl/String",
            "../../Client/Lib/vDesk/Controls/EditControl/Text",
            "../../Client/Lib/vDesk/Controls/EditControl/Email",
            "../../Client/Lib/vDesk/Controls/EditControl/Suggest",
            "../../Client/Lib/vDesk/Controls/SuggestionTextBox",
            "vDesk/Contact"
        ]
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}