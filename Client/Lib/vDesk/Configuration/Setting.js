"use strict";
/**
 * Fired if the Setting has been saved.
 * @event vDesk.Configuration.Setting#save
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'save' event.
 * @property {vDesk.Configuration.Setting} detail.sender The current instance of the Setting.
 */
/**
 * Initializes a new instance of the Setting class.
 * @class Represents configuration setting.
 * @param {String} [Domain=""] Initializes the Setting with the specified domain.
 * @param {String} [Tag=""] Initializes the Setting with the specified tag.
 * @param {String} [Tooltip=""] Initializes the Setting with the specified tooltip text.
 * @param {String} [Type=vDesk.Struct.Type.String] Initializes the Setting with the specified type.
 * @param {String|Number|Boolean|Date} [Value=null] Initializes the Setting with the specified value.
 * @param {Object} [Validator=null] Initializes the Setting with the specified validator.
 * @param {Boolean} [Enabled=false] Flag indicating whether the Setting is enabled.
 * @param {Boolean} [EditEnabled=false] Flag indicating whether the the value of the Setting can be changed.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Domain Gets or sets the domain of the Setting.
 * @property {String} Tag Gets or sets the tag of the Setting.
 * @property {String} Tooltip Gets or sets the tooltip text of the Setting.
 * @property {String} Type Gets or sets the type of the Setting.
 * @property {?*} Value Gets or sets the value of the Setting.
 * @property {?Object|Array} Validator Gets or sets the validator of the Setting.
 * @property {Boolean} EditEnabled Gets or sets a value indicating whether the the value of the Setting can be changed.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Setting is enabled.
 * @property {Boolean} EditEnabled Gets or sets a value indicating whether the Setting is enabled.
 * @extends vDesk.Controls.EditControl
 * @memberOf vDesk.Configuration
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Configuration.Setting = function Setting(
    Domain      = "",
    Tag         = "",
    Tooltip     = null,
    Type        = vDesk.Struct.Type.String,
    Value       = null,
    Validator   = null,
    Enabled     = false,
    EditEnabled = false
) {
    Ensure.Parameter(Domain, vDesk.Struct.Type.String, "Domain");

    /**
     * Flag indicating whether the value of the Setting has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    Object.defineProperties(this, {
        Control:     {
            enumerable: true,
            get:        () => Control
        },
        ToolTip:     {
            enumerable: true,
            get:        () => EditControl.ToolTip,
            set:        Value => EditControl.ToolTip = Value
        },
        Type:        {
            enumerable: true,
            get:        () => EditControl.Type,
            set:        Value => EditControl.Type = Value
        },
        Value:       {
            enumerable: true,
            get:        () => EditControl.Value,
            set:        Value => EditControl.Value = Value
        },
        Validator:   {
            enumerable: true,
            get:        () => EditControl.Validator,
            set:        Value => EditControl.Validator = Value
        },
        Valid:       {
            enumerable: true,
            get:        () => EditControl.Valid,
            set:        Value => EditControl.Valid = Value
        },
        Required:    {
            enumerable: true,
            get:        () => EditControl.Required,
            set:        Value => EditControl.Required = Value
        },
        Domain:      {
            enumerable: true,
            get:        () => Domain,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Domain");
                Domain = Value;
            }
        },
        Tag:         {
            enumerable: true,
            get:        () => EditControl.Label,
            set:        Value => EditControl.Label = Value
        },
        Enabled:     {
            enumerable: true,
            get:        () => EditControl.Enabled,
            set:        Value => EditControl.Enabled = Value && EditEnabled
        },
        EditEnabled: {
            enumerable: true,
            get:        () => EditEnabled,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Enabled");
                EditEnabled = Value;
                EditSaveButton.disabled = !Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#update
     */
    const OnUpdate = () => {
        Changed = true;
        EditSaveButton.title = vDesk.Locale["vDesk"]["Save"];
        EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Save || vDesk.Visual.Icons.Unknown}")`;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Configuration.Setting#save
     */
    const OnClick = () => {
        if(!EditControl.Enabled) {
            EditControl.Enabled = true;
            EditSaveButton.title = vDesk.Locale["vDesk"]["Cancel"];
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel || vDesk.Visual.Icons.Unknown}")`;
        } else {
            EditControl.Enabled = false;
            //Check if the value has been changed.
            if(Changed) {
                Changed = false;
                new vDesk.Events.BubblingEvent("save", {sender: this}).Dispatch(Control);
            }
            EditSaveButton.title = vDesk.Locale["vDesk"]["Edit"];
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Setting BorderLight";

    /**
     * The EditControl of the Setting.
     * @type {vDesk.Controls.EditControl}
     */
    const EditControl = new vDesk.Controls.EditControl(Tag, Tooltip, Type, Value, Validator, true, Enabled && EditEnabled);
    EditControl.Control.addEventListener("update", OnUpdate, false);
    Control.appendChild(EditControl.Control);

    /**
     * The edit/save button of the Setting.
     * @type {HTMLButtonElement}
     */
    const EditSaveButton = document.createElement("button");
    EditSaveButton.className = "Button Icon";
    EditSaveButton.title = vDesk.Locale["vDesk"]["Edit"];
    EditSaveButton.disabled = !EditEnabled;
    EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
    EditSaveButton.addEventListener("click", OnClick, false);
    Control.appendChild(EditSaveButton);

};
