"use strict";
/**
 * Fired if the Permission has been updated.
 * @event vDesk.Security.Group.Permission#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Security.Group.Permission} detail.sender The current instance of the Permission.
 */
/**
 * Initializes a new instance of the Permission class.
 * @class Represents a Permission of a Group.
 * @param {String} [Name=""] Initializes the Permission with the specified name.
 * @param {String} [Description=""] Initializes the Permission with the specified description.
 * @param {Boolean} [Value=false] Initializes the Permission with the specified value.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Permission is enabled.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {String} Name Gets or sets the name of the Permission.
 * @property {String} Description Gets or sets the description of the Permission.
 * @property {Boolean} Value Gets or sets the value of the Permission.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Permission is enabled.
 * @memberOf vDesk.Security.Group
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Security
 */
vDesk.Security.Group.Permission = function Permission(Name = "", Description = "", Value = false, Enabled = true) {
    Ensure.Parameter(Name, Type.String, "Name");
    Ensure.Parameter(Description, Type.String, "Tooltip");
    Ensure.Parameter(Value, Type.Boolean, "Value");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:     {
            enumerable: true,
            get:        () => Control
        },
        Name:        {
            enumerable: true,
            get:        () => NameSpan.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                NameSpan.textContent = Value;
            }
        },
        Description: {
            enumerable: true,
            get:        () => DescriptionSpan.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Description");
                DescriptionSpan.textContent = Value;
            }
        },
        Value:       {
            enumerable: true,
            get:        () => ValueCheckBox.checked,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Value");
                ValueCheckBox.checked = Value;
            }
        },
        Enabled:     {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                ValueCheckBox.disabled = !Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.Group.Permission#update
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Permission BorderLight";

    /**
     * The value checkbox of the Permission.
     * @type {HTMLInputElement}
     */
    const ValueCheckBox = document.createElement("input");
    ValueCheckBox.type = "checkbox";
    ValueCheckBox.className = "Value CheckBox BorderDark";
    ValueCheckBox.checked = Value;
    ValueCheckBox.disabled = !Enabled;
    ValueCheckBox.addEventListener("click", OnClick, false);

    /**
     * The name span of the Permission.
     * @type {HTMLSpanElement}
     */
    const NameSpan = document.createElement("span");
    NameSpan.className = "Name Font Dark";
    NameSpan.textContent = Name;

    /**
     * The description span of the Permission.
     * @type {HTMLSpanElement}
     */
    const DescriptionSpan = document.createElement("span");
    DescriptionSpan.className = "Description Font Dark";
    DescriptionSpan.textContent = Description;

    Control.appendChild(ValueCheckBox);
    Control.appendChild(NameSpan);
    Control.appendChild(DescriptionSpan);

};

/**
 * Factory method that creates a Permission from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Permission.
 * @return {vDesk.Security.Group.Permission} A Permission filled with the provided data.
 */
vDesk.Security.Group.Permission.FromDataView = function(DataView) {
    Ensure.Parameter(DataView, Type.Object, "DataView");
    return new vDesk.Security.Group.Permission(
        DataView?.Name ?? "",
        DataView?.Description ?? "",
        DataView?.Value ?? false
    );
};