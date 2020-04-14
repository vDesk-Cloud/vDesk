"use strict";
/**
 * Fired if the Item has been selected.
 * @event vDesk.Search.ResultList.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Search.ResultList.Item} detail.sender The current instance of the Item.
 */
/**
 * Fired if the Item has been opened.
 * @event vDesk.Search.ResultList.Item#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.Search.ResultList.Item} detail.sender The current instance of the Item.
 */
/**
 * Initializes a new instance of the ResultListItem class.
 * @class Represents an item inside of a ResultList.
 * @param {Object} Result The result of the ResultListItem.
 * @param {Boolean} [Enabled=true] Flag indicating whether the ResultListItem is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {?Object} Result Gets or sets the result of the ResultListItem.
 * @property {?Blob} Icon Gets or sets the icon of the ResultListItem.
 * @property {?String} Name Gets or sets the name of the ResultListItem.
 * @property {?String} Type Gets or sets the type of the ResultListItem.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the ResultListItem is enabled.
 * @memberOf vDesk.Search
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Search.ResultList.Item = function Item(Result, Enabled = true) {
    Ensure.Parameter(Result, vDesk.Struct.Type.Object, "Result");
    Ensure.Parameter(Enabled, vDesk.Struct.Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Result:  {
            enumerable: true,
            get:        () => Result,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Object, "Result");
                Result = Value;
                Icon.src = Value.Icon;
                Name.textContent = Value.Name;
            }
        },
        Icon:    {
            enumerable: true,
            get:        () => Icon.src,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Icon");
                Icon.src = Value;
            }
        },
        Type:    {
            enumerable: true,
            get:        () => Type,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Type");
                Type = Value;
            }
        },
        Name:    {
            enumerable: true,
            get:        () => Name.textContent,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Name");
                Name.textContent = Value;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Enabled");
                Enabled = Value;
                if(Enabled) {
                    Control.addEventListener("click", OnClick, false);
                } else {
                    Control.removeEventListener("click", OnClick, false);
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Search.ResultList.Item#event:select
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'dblclick' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Search.ResultList.Item#event:open
     */
    const OnDblClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("open", {sender: this}).Dispatch(Control);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item";
    Control.addEventListener("click", OnClick, false);
    Control.addEventListener("dblclick", OnDblClick, true);

    /**
     * The icon of the ResultListItem.
     * @type {HTMLImageElement}
     */
    const Icon = document.createElement("img");
    Icon.className = "Icon";
    Icon.src = Result.Icon;

    /**
     * The name span of the ResultListItem.
     * @type {HTMLSpanElement}
     */
    const Name = document.createElement("span");
    Name.className = "Name";
    Name.textContent = Result.Name;

    /**
     * The type of the associated result of the ResultListItem.
     * @type {String}
     */
    let Type = Result.Type;

    Control.appendChild(Icon);
    Control.appendChild(Name);
};