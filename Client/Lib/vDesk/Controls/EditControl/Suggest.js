"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.Suggest#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.Suggest} detail.sender The current instance of the IEditor.
 * @property {String} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.Suggest#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.Suggest} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the Enum class.
 * @class Represents an 'autocomplete' type value editor of an EditControl.
 * @param {String} [Value=null] Initializes the IEditor with the specified value.
 * @param {Array} [Validator=null] Initializes the IEditor with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the IEditor requires a value.
 * @param {Boolean} [Enabled=false] Flag indicating whether the IEditor is enabled.
 * @property {HTMLInputElement} Control Gets the underlying DOM-Node.
 * @property {?String} Value Gets or sets the value of the IEditor.
 * @property {?Array} Validator Gets or sets the validator of the IEditor.
 * @property {Boolean} Valid Gets or sets a value indicating whether the value of the IEditor is valid.
 * @property {Boolean} Required Gets or sets a value indicating whether the IEditor requires a value.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the IEditor is enabled.
 * @memberOf vDesk.Controls.EditControl
 * @implements vDesk.Controls.EditControl.IEditor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl.Suggest = function Suggest(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, Type.String, "Value", true);
    Ensure.Parameter(Validator, Array, "Validator", true);
    Ensure.Parameter(Required, Type.Boolean, "Required");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => SuggestionTextBox.Control
        },
        Value:     {
            enumerable: true,
            get:        () => Value,
            set:        ValueToSet => {
                Ensure.Property(ValueToSet, Type.String, "Value", true);
                Value = ValueToSet;
                SuggestionTextBox.Value = Value || "";
            }
        },
        Validator: {
            enumerable: true,
            get:        () => SuggestionTextBox.Suggestions,
            set:        Value => SuggestionTextBox.Suggestions = Value
        },
        Valid:     {
            enumerable: true,
            get:        () => {
                if(!Required && SuggestionTextBox.Value === "") {
                    return true;
                }
                return ~SuggestionTextBox.Suggestions.indexOf(SuggestionTextBox.Value);
            },
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Valid");
                SuggestionTextBox.Control.classList.toggle("Error", !Value);
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
            get:        () => SuggestionTextBox.Enabled,
            set:        Value => SuggestionTextBox.Enabled = Value
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.SuggestionTextBox#event:update
     * @fires vDesk.Controls.EditControl.Suggest#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        SuggestionTextBox.Control.removeEventListener("update", OnUpdate, false);
        Value = Event.detail.value;
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            value:  Event.detail.value
        }).Dispatch(SuggestionTextBox.Control);
        SuggestionTextBox.Control.addEventListener("update", OnUpdate, false);
    };

    /**
     * Eventhandler that listens on the 'clear' event.
     * @listens vDesk.Controls.SuggestionTextBox#event:clear
     * @fires vDesk.Controls.EditControl.Suggest#clear
     * @param {CustomEvent} Event
     */
    const OnClear = Event => {
        Event.stopPropagation();
        SuggestionTextBox.Control.removeEventListener("clear", OnClear, false);
        Value = null;
        new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(SuggestionTextBox.Control);
        SuggestionTextBox.Control.addEventListener("clear", OnClear, false);
    };

    /**
     * The SuggestionTextBox of the IEditor.
     * @type {vDesk.Controls.SuggestionTextBox}
     */
    const SuggestionTextBox = new vDesk.Controls.SuggestionTextBox(Value || "", Validator, Enabled);
    SuggestionTextBox.Control.classList.add("Suggest");
    SuggestionTextBox.Control.addEventListener("update", OnUpdate, false);

};
vDesk.Controls.EditControl.Suggest.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.Suggest.Types = [Extension.Type.Suggest];