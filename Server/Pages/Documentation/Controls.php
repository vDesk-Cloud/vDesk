<?php
declare(strict_types=1);

namespace Pages\Documentation;

use Pages\Documentation;

/**
 * Documentation\Controls Page.
 *
 * @package Pages\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Controls extends Documentation {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Controls";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Controls";
    
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
        ?iterable $Templates = ["Documentation"],
        ?iterable $Stylesheets = [
            "Documentation/Stylesheet",
            "vDesk/Stylesheet",
            "../../Client/Design/vDesk",
            "../../Client/Design/vDesk/Controls/GroupBox",
            "../../Client/Design/vDesk/Controls/ResizableBox",
            "../../Client/Design/vDesk/Controls/DynamicBox",
            "../../Client/Design/vDesk/Controls/Resizer",
            "../../Client/Design/vDesk/Controls/ToolBar",
            "../../Client/Design/vDesk/Controls/ToolBar/Group",
            "../../Client/Design/vDesk/Controls/ToolBar/Item",
            "../../Client/Design/vDesk/Controls/TabControl",
            "../../Client/Design/vDesk/Controls/TabControl/TabItem",
            "../../Client/Design/vDesk/Controls/Table",
            "../../Client/Design/vDesk/Controls/Table/Cell",
            "../../Client/Design/vDesk/Controls/Table/Row",
            "../../Client/Design/vDesk/Controls/Calendar",
            "../../Client/Design/vDesk/Controls/Calendar/Cell",
            "../../Client/Design/vDesk/Controls/Calendar/View/Day",
            "../../Client/Design/vDesk/Controls/Calendar/View/Month",
            "../../Client/Design/vDesk/Controls/Calendar/View/Year",
            "../../Client/Design/vDesk/Controls/Calendar/View/Decade",
            "../../Client/Design/vDesk/Controls/DatePicker",
            "../../Client/Design/vDesk/Controls/DateTimePicker",
            "../../Client/Design/vDesk/Controls/TimePicker",
            "../../Client/Design/vDesk/Controls/TimeSpanPicker",
            "../../Client/Design/vDesk/Controls/ContextMenu",
            "../../Client/Design/vDesk/Controls/ContextMenu/Item",
            "../../Client/Design/vDesk/Controls/ContextMenu/Group",
            "../../Client/Design/vDesk/Controls/SuggestionTextBox",
            "../../Client/Design/vDesk/Controls/EditControl",
            "../../Client/Design/vDesk/Controls/EditControl/Text",
            "../../Client/Design/vDesk/Controls/EditControl/Suggest",
            "../../Client/Design/vDesk/Controls/EditControl/Boolean",
            "../../Client/Design/vDesk/Media/Drawing/ColorPicker",
            "../../Client/Design/vDesk/Controls/EditControl/Color",
            "../../Client/Design/vDesk/Controls/EditControl/Date",
            "../../Client/Design/vDesk/Controls/EditControl/Time",
            "../../Client/Design/vDesk/Controls/EditControl/Money",
            "../../Client/Design/vDesk/Controls/EditControl/DateTime",
            "../../Client/Design/vDesk/Controls/EditControl/TimeSpan",
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
            "../../Client/Lib/vDesk/Visual/TreeHelper",
            "../../Client/Lib/vDesk/Visual/Animation/Animation",
            "../../Client/Lib/vDesk/Controls",
            "../../Client/Lib/vDesk/Controls/Calendar",
            "../../Client/Lib/vDesk/Controls/Calendar/Cell",
            "../../Client/Lib/vDesk/Controls/Calendar/IView",
            "../../Client/Lib/vDesk/Controls/Calendar/View",
            "../../Client/Lib/vDesk/Controls/Calendar/View/Day",
            "../../Client/Lib/vDesk/Controls/Calendar/View/Month",
            "../../Client/Lib/vDesk/Controls/Calendar/View/Year",
            "../../Client/Lib/vDesk/Controls/Calendar/View/Decade",
            "../../Client/Lib/vDesk/Controls/Calendar/IEvent",
            "../../Client/Lib/vDesk/Controls/Calendar/Event",
            "../../Client/Lib/vDesk/Controls/ContextMenu",
            "../../Client/Lib/vDesk/Controls/ContextMenu/Item",
            "../../Client/Lib/vDesk/Controls/ContextMenu/Group",
            "../../Client/Lib/vDesk/Controls/Resizer",
            "../../Client/Lib/vDesk/Controls/ToolBar",
            "../../Client/Lib/vDesk/Controls/ToolBar/Item",
            "../../Client/Lib/vDesk/Controls/ToolBar/Group",
            "../../Client/Lib/vDesk/Controls/TabControl",
            "../../Client/Lib/vDesk/Controls/TabControl/TabItem",
            "../../Client/Lib/vDesk/Controls/Table",
            "../../Client/Lib/vDesk/Controls/Table/IRow",
            "../../Client/Lib/vDesk/Controls/Table/Row",
            "../../Client/Lib/vDesk/Controls/Table/RowCollection",
            "../../Client/Lib/vDesk/Controls/Table/Column",
            "../../Client/Lib/vDesk/Controls/Table/ColumnCollection",
            "../../Client/Lib/vDesk/Controls/Table/Cell",
            
            //Grouping
            "../../Client/Lib/vDesk/Controls/GroupBox",
            "../../Client/Lib/vDesk/Controls/FloatingBox",
            "../../Client/Lib/vDesk/Controls/ResizableBox",
            "../../Client/Lib/vDesk/Controls/DynamicBox",
            
            //Picker
            "../../Client/Lib/vDesk/Controls/DatePicker",
            "../../Client/Lib/vDesk/Controls/TimePicker",
            "../../Client/Lib/vDesk/Controls/DateTimePicker",
            "../../Client/Lib/vDesk/Controls/TimeSpanPicker",
            "../../Client/Lib/vDesk/Media",
            "../../Client/Lib/vDesk/Media/Drawing",
            "../../Client/Lib/vDesk/Media/Drawing/Color",
            "../../Client/Lib/vDesk/Media/Drawing/ColorPicker",
            
            //Input
            "../../Client/Lib/vDesk/Controls/ContextMenu",
            "../../Client/Lib/vDesk/Controls/ContextMenu/Item",
            "../../Client/Lib/vDesk/Controls/ContextMenu/Group",
            "../../Client/Lib/vDesk/Controls/SuggestionTextBox",
            "../../Client/Lib/vDesk/Controls/EditControl",
            "../../Client/Lib/vDesk/Controls/EditControl/Text",
            "../../Client/Lib/vDesk/Controls/EditControl/String",
            "../../Client/Lib/vDesk/Controls/EditControl/Email",
            "../../Client/Lib/vDesk/Controls/EditControl/URL",
            "../../Client/Lib/vDesk/Controls/EditControl/Suggest",
            "../../Client/Lib/vDesk/Controls/EditControl/Password",
            "../../Client/Lib/vDesk/Controls/EditControl/Color",
            "../../Client/Lib/vDesk/Controls/EditControl/Number",
            "../../Client/Lib/vDesk/Controls/EditControl/Range",
            "../../Client/Lib/vDesk/Controls/EditControl/Money",
            "../../Client/Lib/vDesk/Controls/EditControl/Boolean",
            "../../Client/Lib/vDesk/Controls/EditControl/Date",
            "../../Client/Lib/vDesk/Controls/EditControl/Time",
            "../../Client/Lib/vDesk/Controls/EditControl/DateTime",
            "../../Client/Lib/vDesk/Controls/EditControl/TimeSpan",
            "../../Client/Lib/vDesk/Controls/EditControl/Enum",
        ]
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}