"use strict";
/**
 * Fired if the Item has been selected.
 * @event vDesk.Controls.ContextMenu.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Controls.ContextMenu.Item} detail.sender The current instance of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 * @class Represents an Item of a ContextMenu.
 * @param {String} [Name=""] Initializes the Item with the specified name.
 * @param {String} [Action=""] Initializes the Item with the specified action.
 * @param {String} [Icon=""] Initializes the Item with the specified icon.
 * @param {Function} [Condition=vDesk.Controls.ContextMenu.Item.DefaultCondition] Initializes the Item with the specified condition callback.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {?String} Name Gets or sets the name of the Item.
 * @property {?String} Action Gets or sets the action of the Item.
 * @property {?Blob} Icon Gets or sets the icon of the Item.
 * @property {Function} Condition Gets or sets the condition callback of the Item.
 * @property {Boolean} Visible Gets or sets a value indicating whether the Item is visible.
 * @memberOf vDesk.Controls.ContextMenu
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.ContextMenu.Item = function Item(Name = "", Action = "", Icon = "", Condition = vDesk.Controls.ContextMenu.Item.DefaultCondition) {
    Ensure.Parameter(Name, Type.String, "Name");
    Ensure.Parameter(Action, Type.String, "Action");
    Ensure.Parameter(Icon, Type.String, "Icon");
    Ensure.Parameter(Condition, Type.Function, "Condition");

    Object.defineProperties(this, {
        Control:   {
            enumerable: true,
            get:        () => Control
        },
        Icon:      {
            enumerable: true,
            get:        () => IconImage.src,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Icon");
                IconImage.src = Value;
            }
        },
        Name:      {
            enumerable: true,
            get:        () => NameSpan.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                NameSpan.textContent = Value;
            }
        },
        Action:    {
            enumerable:   true,
            configurable: true,
            get:          () => Action,
            set:          Value => {
                Ensure.Property(Value, Type.String, "Action");
                Action = Value;
            }
        },
        Condition: {
            enumerable: true,
            get:        () => Condition,
            set:        Value => {
                Ensure.Property(Value, Type.Function, "Condition");
                Condition = Value;
            }
        },
        Visible:   {
            enumerable: true,
            get:        () => Control.style.display === "list-item",
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Visible");
                if(Value) {
                    Control.style.display = "list-item";
                } else {
                    Control.style.display = "none";
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event and emits the select event if the Item has been clicked on.
     * @fires vDesk.Controls.ContextMenu.Item#select
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
    };

    /**
     * Shows the Item according a specified ContextMenu target.
     * @param Target The current ContextMenu target.
     */
    this.Show = function(Target) {
        this.Visible = Condition(Target);
    };

    /**
     * Hides the
     * @constructor
     */
    this.Hide = function() {
        this.Visible = false;
    };

    /**
     * The underlying control.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item";
    Control.addEventListener("click", OnClick);

    /**
     * The icon control of the Item.
     * @type {HTMLImageElement}
     */
    const IconImage = document.createElement("img");
    IconImage.className = "Icon";
    IconImage.src = Icon;

    /**
     * The name span of the Item.
     * @type {HTMLSpanElement}
     */
    const NameSpan = document.createElement("span");
    NameSpan.className = "Title";

    NameSpan.textContent = Name;

    Control.appendChild(IconImage);
    Control.appendChild(NameSpan);
};

/**
 * Default callback.
 * Displays the Item if no condition has been passed.
 * @constant
 * @type Function
 * @name vDesk.Controls.ContextMenu.Item.DefaultCondition
 */
vDesk.Controls.ContextMenu.Item.DefaultCondition = () => true;
