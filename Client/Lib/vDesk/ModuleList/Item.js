"use strict";
/**
 * Fired if the Item has been selected.
 * @event vDesk.ModuleList.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.ModuleList.Item} detail.sender The current instance of the Item.
 * @property {vDesk.Modules.IVisualModule} detail.module The Module of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 * @class Represents a menu-Item within the Menu of the client.
 * @param {vDesk.Modules.IVisualModule} Module Initializes the Item with the specified Module.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Item is enabled.
 * @property {HTMLButtonElement} Controls Gets the underlying DOM-Node.
 * @property {vDesk.Modules.IVisualModule} Module Gets the Module of the Item.
 * @property {String} Name Gets the name of the Item.
 * @property {String} Icon Gets the icon of the Item.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the item is enabled.
 * @property {Boolean} Selected Gets or sets a value indicating whether the item is selected.
 * @memberOf vDesk.Menu
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.ModuleList.Item = function Item(Module, Enabled = true) {
    Ensure.Parameter(Module, vDesk.Modules.IVisualModule, "Module");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the Item is selected.
     * @type {Boolean}
     */
    let Selected = false;

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Name:     {
            enumerable: true,
            get:        () => Module.Name
        },
        Icon:     {
            enumerable: true,
            get:        () => Module.Icon
        },
        Module:   {
            enumerable: true,
            get:        () => Module,
            set:        Value => {
                Ensure.Property(Value, vDesk.Modules.IVisualModule, "Module");
                Module = Value;
                Control.textContent = Value.Name;
                Control.style.backgroundImage = `url("${Value.Icon}")`;
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Selected);
            }
        },
        Enabled:  {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                if(Value) {
                    Control.addEventListener("click", OnClick, false);
                } else {
                    Control.removeEventListener("click", OnClick, false);
                }
                Control.classList.toggle("Disabled", !Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.ModuleList.Item#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent(
        "select",
        {
            sender: this,
            module: Module
        },
        true
    ).Dispatch(Control);

    /**
     * The underlying DOM-Node.
     * @type {HTMLButtonElement}
     */
    const Control = document.createElement("button");
    Control.className = "Item Control Font Dark Background BorderLight";
    Control.classList.toggle("Disabled", !Enabled);
    Control.textContent = Module.Title;
    Control.style.backgroundImage = `url("${Module.Icon}")`;
    Control.disabled = !Enabled;
    if(Enabled) {
        Control.addEventListener("click", OnClick, false);
    }
};