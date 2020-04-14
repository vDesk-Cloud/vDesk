"use strict";
/**
 * Fired if the Item has been selected.
 * @event vDesk.MetaInformation.MaskList.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.MetaInformation.MaskList.Item} detail.sender The current instance of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 * @class Represents an Item inside a {@link vDesk.MetaInformation.MaskList|MaskList}.
 * @param {vDesk.MetaInformation.Mask} Mask Initializes the Item with the specified Mask.
 * @param {Boolean} [Selected=false] Flag indicating whether the Item is selected.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Item is enabled.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.MetaInformation.Mask} Mask Gets or sets the Mask of the Item.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Item is selected.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Item is enabled.
 * @memberOf vDesk.MetaInformation.MaskList
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.MaskList.Item = function Item(Mask, Selected = false, Enabled = true) {
    Ensure.Parameter(Mask, vDesk.MetaInformation.Mask, "Mask");
    Ensure.Parameter(Selected, Type.Boolean, "Selected");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Mask:    {
            enumerable: true,
            get:        () => Mask,
            set:        Value => {
                Ensure.Property(Value, vDesk.MetaInformation.Mask, "Mask", true);
                Mask = Value;
                Control.textContent = Mask.Name || "";
            }
        },
        Selected:  {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Control.classList.toggle("Disabled", !Value);
                if(Enabled) {
                    Control.addEventListener("click", OnClick, false);
                    Control.classList.toggle("Selected", Selected);
                } else {
                    Control.removeEventListener("click", OnClick, false);
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.MetaInformation.MaskList.Item#select
     */
    const OnClick = () => {new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control)};

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item Font Dark BorderLight";
    Control.textContent = Mask.Name;
    Control.addEventListener("click", OnClick, false);

    this.Selected = Selected;
    this.Enabled = Enabled;
};