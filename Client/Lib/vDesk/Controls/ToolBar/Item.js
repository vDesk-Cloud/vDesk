"use strict";
/**
 * Initializes a new instance of the Item class.
 * @class Represents an Item of a ToolBar.
 * @param {String} [Label=""] Initializes the Item with the specified label.
 * @param {String} [Icon=""] Initializes the Item with the specified icon.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Item is enabled.
 * @param {?Function} [Callback=null] Initializes the Item with the specified callback.
 * @property {HTMLButtonElement} Control Gets the underlying DOM-Node.
 * @property {String} Label Gets or sets the label of the Item.
 * @property {String} Icon Gets or sets the icon of the Item.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Item is selected.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Item is enabled.
 * @property {?Function|null} Callback Gets or Sets the callback to execute if the Item has been clicked on.
 * @memberOf vDesk.Controls.ToolBar
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.ToolBar.Item = function Item(Label = "", Icon = "", Enabled = true, Callback = null) {
    Ensure.Parameter(Label, Type.String, "Label");
    Ensure.Parameter(Icon, Type.String, "Icon");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");
    Ensure.Parameter(Callback, Type.Function, "Callback", true);

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
        Label:    {
            get: () => Control.textContent,
            set: Value => {
                Ensure.Property(Value, Type.String, "Label");
                Control.textContent = Value;
            }
        },
        Icon:     {
            get: () => Icon,
            set: Value => {
                Ensure.Property(Value, Type.String, "Icon");
                Icon = Value;
                Control.style.backgroundImage = `url("${Value}")`;
            }
        },
        Selected:  {
            get: () => Selected,
            set: Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Enabled:  {
            get: () => Enabled,
            set: Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Control.disabled = !Value;
                Control.classList.toggle("Dark", Value);
                Control.classList.toggle("Disabled", !Value);
            }
        },
        Callback: {
            get: () => Control.onclick,
            set: Value => {
                Ensure.Property(Value, Type.Function, "Callback", true);
                Control.onclick = Value;
            }
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLButtonElement}
     */
    const Control = document.createElement("button");
    Control.className = "Item Control Font Background";
    Control.textContent = Label;
    Control.style.backgroundImage = `url("${Icon}")`;
    Control.disabled = !Enabled;
    Control.classList.toggle("Dark", Enabled);
    Control.classList.toggle("Disabled", !Enabled);
    Control.onclick = Callback;
};

