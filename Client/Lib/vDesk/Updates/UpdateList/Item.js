"use strict";
/**
 * Fired if the Item has been selected.
 * @event vDesk.Updates.UpdateList.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Updates.UpdateList.Item} detail.sender The current instance of the Item.
 * @property {vDesk.Updates.Update} detail.user The Update of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 * @class Represents an Item inside the UpdateList.
 * @param {vDesk.Updates.Update} Update Initializes the Item with the specified Update.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Item is enabled.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Updates.Update} Update Gets or sets the Update of the Item.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Item is enabled.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Item is selected.
 * @memberOf vDesk.Updates.UpdateList
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Updates.UpdateList.Item = function Item(Update, Enabled = true) {
    Ensure.Parameter(Update, vDesk.Updates.Update, "Update");
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
        Update:  {
            enumerable: true,
            get:        () => Update,
            set:        Value => {
                Ensure.Property(Value, vDesk.Updates.Update, "Update");
                Update = Value;
                Name.textContent = `${Value.Package} [${Value.RequiredVersion}]`;
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
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
                Control.draggable = Value;
                Control.style.cursor = Value ? "grab" : "pointer";
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Updates.UpdateList.Item#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("select", {
        sender:  this,
        package: Update
    }).Dispatch(Control);

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item Font Dark BorderLight";
    Control.classList.toggle("Disabled", !Enabled);
    Control.draggable = Enabled;
    Control.style.cursor = Enabled ? "grab" : "pointer";
    if(Enabled) {
        Control.addEventListener("click", OnClick, false);
    }

    /**
     * The name span of the Item.
     * @type {HTMLSpanElement}
     */
    const Name = document.createElement("span");
    Name.textContent = `${Update.Package} [${Update.RequiredVersion}]`;
    Control.appendChild(Name);

};