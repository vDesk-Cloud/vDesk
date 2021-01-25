"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.Color#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.Color} detail.sender The current instance of the IEditor.
 * @property {vDesk.Media.Drawing.Color} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.Color#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.Color} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the Color class.
 * @class Represents a Color type value editor of an EditControl.
 * @param {vDesk.Media.Drawing.Color} [Value=null] Initializes the IEditor with the specified value.
 * @param {Object} [Validator=null] Initializes the IEditor with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the IEditor requires a value.
 * @param {Boolean} [Enabled=false] Flag indicating whether the IEditor is enabled.
 * @property {HTMLInputElement} Control Gets the underlying DOM-Node.
 * @property {?vDesk.Media.Drawing.Color} Value Gets or sets the value of the IEditor.
 * @property {?Object} Validator Gets or sets the validator of the IEditor.
 * @property {Boolean} Valid Gets or sets a value indicating whether the value of the IEditor is valid.
 * @property {Boolean} Required Gets or sets a value indicating whether the IEditor requires a value.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the IEditor is enabled.
 * @memberOf vDesk.Controls.EditControl
 * @implements vDesk.Controls.EditControl.IEditor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl.Color = function Color(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, vDesk.Media.Drawing.Color, "Value", true);
    Ensure.Parameter(Validator, Type.Object, "Validator", true);
    Ensure.Parameter(Required, Type.Boolean, "Required");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the calendar of the DatePicker is displayed.
     * @type {Boolean}
     */
    let Expanded = false;

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => Control
        },
        Value:     {
            enumerable: true,
            get:        () => Value,
            set:        ValueToSet => {
                Ensure.Property(ValueToSet, vDesk.Media.Drawing.Color, "Value", true);
                Value = ValueToSet;
                ColorPicker.Color = ValueToSet ?? new vDesk.Media.Drawing.Color();
                Button.style.color = TextBox.value = ColorPicker.Color.ToHexString();
            }
        },
        Validator: {
            enumerable: true,
            get:        () => Validator,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Validator", true);
                ColorPicker.Mode = Validator?.Mode ?? vDesk.Media.Drawing.ColorPicker.RGBA | vDesk.Media.Drawing.ColorPicker.HSLA | vDesk.Media.Drawing.ColorPicker.Hex;
                Validator = Value;
            }
        },
        Valid:     {
            enumerable: true,
            get:        () => {
                if(!Required && Value === null) {
                    return true;
                }
                return Value !== null;
            },
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Valid");
                ColorPicker.Control.classList.toggle("Error", !Value);
            }
        },
        Required:  {
            enumerable: true,
            get:        () => Required,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Required");
                Required = Value;
            }
        },
        Enabled:   {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                if(Value) {
                    TextBox.disabled = false;
                    Button.disabled = false;
                } else {
                    TextBox.disabled = true;
                    Button.disabled = true;
                    window.removeEventListener("click", OnClick, false);
                    vDesk.Visual.Animation.FadeOut(ColorPicker.Control, 150, () => ColorPicker.Control.style.display = "none");
                    Button.textContent = "▼";
                    Expanded = false;
                }
                Enabled = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'change' event.
     * @fires vDesk.Controls.EditControl.Color#update
     * @fires vDesk.Controls.EditControl.Color#clear
     */
    const OnChange = () => {
        if(TextBox.value.length > 0) {
            Value = ColorPicker.Color = vDesk.Media.Drawing.Color.FromHexString(TextBox.value);
            Button.style.color = ColorPicker.Color.ToHexString();
            new vDesk.Events.BubblingEvent("update", {
                sender: this,
                value:  ColorPicker.Color
            }).Dispatch(Control);
        } else {
            new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
        }
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @param {CustomEvent} Event
     */
    const OnClick = Event => {
        Event.stopPropagation();
        ToggleColorPicker();
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Media.Drawing.ColorPicker#event:update
     * @fires vDesk.Controls.EditControl.Color#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Value = ColorPicker.Color;
        Button.style.color = TextBox.value = ColorPicker.Color.ToHexString();
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            value:  Event.detail.color
        }).Dispatch(Control);
    };

    /**
     * Toggles the visibility of the ColorPicker of the ColorPicker.
     */
    const ToggleColorPicker = function() {
        if(Expanded) {
            vDesk.Visual.Animation.FadeOut(ColorPicker.Control, 150, () => ColorPicker.Control.style.display = "none");
            window.removeEventListener("click", OnClick, false);
            Button.textContent = "▼";
            Expanded = false;
        } else {
            vDesk.Visual.Animation.FadeIn(ColorPicker.Control, 150, () => ColorPicker.Control.style.display = "block");
            ColorPicker.Control.style.display = "";
            Button.textContent = "▲";
            Expanded = true;
            window.addEventListener("click", OnClick, false);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Editor Color";

    /**
     * The ColorPicker of the ColorPicker.
     * @type {vDesk.Media.Drawing.ColorPicker}
     */
    const ColorPicker = new vDesk.Media.Drawing.ColorPicker(
        Value ?? new vDesk.Media.Drawing.Color(),
        Validator?.Mode ?? vDesk.Media.Drawing.ColorPicker.RGBA | vDesk.Media.Drawing.ColorPicker.HSLA | vDesk.Media.Drawing.ColorPicker.Hex
    );
    ColorPicker.Control.style.display = "none";
    ColorPicker.Control.style.position = "absolute";
    ColorPicker.Control.addEventListener("update", OnUpdate, false);
    ColorPicker.Control.addEventListener("click", Event => Event.stopPropagation(), false);


    /**
     * The TextBox of the ColorPicker.
     * @type {HTMLInputElement}
     */
    const TextBox = document.createElement("input");
    TextBox.type = "text";
    TextBox.className = "TextBox";
    TextBox.value = ColorPicker.Color.ToHexString();
    TextBox.disabled = !Enabled;
    TextBox.addEventListener("change", OnChange, false);

    /**
     * The button of the DatePicker.
     * @type {HTMLButtonElement}
     */
    const Button = document.createElement("button");
    Button.className = "Button Toggle";
    Button.value = "▼";
    Button.textContent = "▼";
    Button.addEventListener("click", OnClick, false);
    Button.disabled = !Enabled;
    Button.style.color = ColorPicker.Color.ToHexString();

    Control.appendChild(TextBox);
    Control.appendChild(Button);
    Control.appendChild(ColorPicker.Control);

};
vDesk.Controls.EditControl.Color.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.Color.Types = [Extension.Type.Color];