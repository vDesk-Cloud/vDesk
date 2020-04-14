"use strict";
/**
 * Fired if the value of the EditControl has been updated.
 * @event vDesk.Controls.EditControl#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl} detail.sender The current instance of the EditControl.
 * @property {*} detail.value The updated value of the EditControl.
 */
/**
 * Fired if the value of the EditControl has been cleared.
 * @event vDesk.Controls.EditControl#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl} detail.sender The current instance of the IEditor.
 */
/**
 * Fired if the EditControl has been saved.
 * @event vDesk.Controls.EditControl#save
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'save' event.
 * @property {vDesk.Controls.EditControl} detail.sender The current instance of the EditControl.
 * @property {String|Number|Boolean|Date} detail.value The value of the EditControl.
 */
/**
 * Initializes a new instance of the EditControl class.
 * @class Represents a typesafe value modifier control.
 * @param {String} [Label=""] Initializes the EditControl with the specified label text.
 * @param {String} [Tooltip=null] Initializes the EditControl with the specified tooltip text.
 * @param {String} [Type=vDesk.Struct.Type.String] Initializes the EditControl with the specified type.
 * @param {*} [Value=null] Initializes the EditControl with the specified value.
 * @param {Object|Array} [Validator=null] Initializes the EditControl with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the EditControl requires a value.
 * @param {Boolean} [Enabled=true] Flag indicating whether the EditControl is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Label Gets or sets the text of the label of the EditControl.
 * @property {?String} Tooltip Gets or sets the text of the tooltip of the EditControl.
 * @property {String} Type Gets or sets the type of the EditControl.
 * @property {?*} Value Gets or sets the value of the EditControl.
 * @property {?Object|Array} Validator Gets or sets the validator of the EditControl.
 * @property {Boolean} Required Gets or sets a value indicating whether the EditControl requires a value.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the EditControl is enabled.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl = function EditControl(
    Label     = "",
    Tooltip   = null,
    Type      = vDesk.Struct.Type.String,
    Value     = null,
    Validator = null,
    Required  = false,
    Enabled   = true
) {
    Ensure.Parameter(Label, vDesk.Struct.Type.String, "Label");
    Ensure.Parameter(Tooltip, vDesk.Struct.Type.String, "Tooltip", true);
    Ensure.Parameter(Type, vDesk.Struct.Type.String, "Type");
    Ensure.Parameter(Validator, [vDesk.Struct.Type.Object, Array], "Validator", true);
    Ensure.Parameter(Enabled, vDesk.Struct.Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the value of the EditControl has been modified.
     * @type {Boolean}
     */
    let Changed = false;

    Object.defineProperties(this, {
        Control:   {
            enumerable: true,
            get:        () => Control
        },
        Label:     {
            enumerable: true,
            get:        () => LabelSpan.textContent,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Label");
                LabelSpan.textContent = Value;
            }
        },
        ToolTip:   {
            enumerable: true,
            get:        () => LabelSpan.title,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "ToolTip", true);
                LabelSpan.title = Value || "";
                LabelSpan.style.cursor = Value !== null ? "help" : "default";
            }
        },
        Type:      {
            enumerable: true,
            get:        () => Type,
            set:        Value => {
                Ensure.Property(Value, [vDesk.Struct.Type.String, vDesk.Struct.Type.Function], "Type");
                Type = Value;
                const IEditor = Object.values(vDesk.Controls.EditControl).find(IEditor => ~(IEditor.Types || []).indexOf(Value));
                if(IEditor === undefined) {
                    throw new ArgumentError(`'${Value}' is not a supported type!`);
                }
                const Previous = Editor;
                Editor = new IEditor(Editor.Value, Validator, Required, Enabled);
                Control.replaceChild(Editor.Control, Previous.Control);
            }
        },
        Value:     {
            enumerable: true,
            get:        () => Editor.Value,
            set:        Value => Editor.Value = Value
        },
        Validator: {
            enumerable: true,
            get:        () => Editor.Validator,
            set:        Value => Editor.Validator = Value
        },
        Valid:     {
            enumerable: true,
            get:        () => Editor.Valid,
            set:        Value => Editor.Valid = Value
        },
        Required:  {
            enumerable: true,
            get:        () => Editor.Required,
            set:        Value => Editor.Required = Value
        },
        Enabled:   {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Enabled");
                Enabled = Value;
                Editor.Enabled = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl.IEditor#event:update
     * @fires vDesk.Controls.EditControl#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Changed = true;
        if(Editor.Valid) {
            Control.removeEventListener("update", OnUpdate, false);
            new vDesk.Events.BubblingEvent("update", {
                sender: this,
                value:  Event.detail.value
            }).Dispatch(Control);
            Control.addEventListener("update", OnUpdate, false);
            Editor.Valid = true;
        } else {
            Editor.Valid = false;
        }
    };

    /**
     * Eventhandler that listens on the 'clear' event.
     * @listens vDesk.Controls.EditControl.IEditor#event:clear
     * @fires vDesk.Controls.EditControl#clear
     * @param {CustomEvent} Event
     */
    const OnClear = Event => {
        Event.stopPropagation();
        Control.removeEventListener("clear", OnClear, false);
        new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
        Control.addEventListener("clear", OnClear, false);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = `EditControl ${Type}`;
    Control.addEventListener("update", OnUpdate, false);
    Control.addEventListener("clear", OnClear, false);

    /**
     * The label span of the EditControl.
     * @type {HTMLSpanElement}
     */
    const LabelSpan = document.createElement("span");
    LabelSpan.className = "Label Font Dark";
    LabelSpan.textContent = Label;
    LabelSpan.title = Tooltip || "";
    LabelSpan.style.cursor = Tooltip !== null ? "help" : "default";

    Control.appendChild(LabelSpan);

    const IEditor = Object.values(vDesk.Controls.EditControl).find(IEditor => ~(IEditor.Types || []).indexOf(Type));
    if(IEditor === undefined) {
        throw new ArgumentError(`'${Type}' is not a supported type!`);
    }
    /**
     * The IEditor of the EditControl.
     * @type {vDesk.Controls.EditControl.IEditor}
     */
    let Editor = new IEditor(Value, Validator, Required, Enabled);
    Control.appendChild(Editor.Control);

};