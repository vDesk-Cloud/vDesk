"use strict";
/**
 * Fired if the values of the IValidator has been updated.
 * @event vDesk.MetaInformation.Mask.Row.Validator.Number#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.MetaInformation.Mask.Row.Validator.Number} detail.sender The current instance of the IValidator.
 */
/**
 * Initializes a new instance of the Number class.
 * @class Represents a numeric validator.
 * @memberOf vDesk.MetaInformation.Mask.Row.Validator
 * @author Kerry <DevelopmentHero@gmail.com>
 * @implements vDesk.MetaInformation.Mask.Row.IValidator
 * @package vDesk\MetaInformation
 */
vDesk.MetaInformation.Mask.Row.Validator.Number = function Number(Validator = null, Enabled = true) {
    Ensure.Parameter(Validator, Type.Object, "Validator", true);
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:   {
            enumerable: true,
            get:        () => Control
        },
        Min:       {
            enumerable: true,
            get:        () => Min.Value,
            set:        Value => Min.Value = Value
        },
        Max:       {
            enumerable: true,
            get:        () => Max.Value,
            set:        Value => Max.Value = Value
        },
        Steps:     {
            enumerable: true,
            get:        () => Steps.Value,
            set:        Value => Steps.Value = Value
        },
        Validator: {
            enumerable: true,
            get:        () => {
                return {
                    Min:   Min.Value,
                    Max:   Max.Value,
                    Steps: Steps.Value
                };
            },
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Validator", true);
                Min.Value = Value?.Min ?? null;
                Max.Value = Value?.Max ?? window.Number.MAX_SAFE_INTEGER;
                Steps.Value = Value?.Steps ?? 1;
            }
        },
        Enabled:   {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Min.Enabled = Value;
                Max.Enabled = Value;
                Steps.Enabled = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#event:update
     * @fires vDesk.MetaInformation.Mask.Row.Validator.Money#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);
        Control.addEventListener("update", OnUpdate, {once: true});
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Validator Number";
    Control.addEventListener("update", OnUpdate, {once: true});

    /**
     * The min EditControl of the Money validator.
     * @type {vDesk.Controls.EditControl}
     */
    const Min = new vDesk.Controls.EditControl(
        `${vDesk.Locale.MetaInformation.Min}:`,
        null,
        Type.Number,
        Validator?.Min ?? null,
        null,
        false,
        Enabled
    );
    Min.Control.classList.add("Min");
    Control.appendChild(Min.Control);

    /**
     * The min EditControl of the Money validator.
     * @type {vDesk.Controls.EditControl}
     */
    const Max = new vDesk.Controls.EditControl(
        `${vDesk.Locale.MetaInformation.Max}:`,
        null,
        Type.Number,
        Validator?.Max ?? window.Number.MAX_SAFE_INTEGER,
        null,
        false,
        Enabled
    );
    Max.Control.classList.add("Max");
    Control.appendChild(Max.Control);

    /**
     * The steps EditControl of the Money validator.
     * @type {vDesk.Controls.EditControl}
     */
    const Steps = new vDesk.Controls.EditControl(
        `${vDesk.Locale.MetaInformation.Steps}:`,
        null,
        Type.Number,
        Validator?.Steps ?? 1,
        null,
        false,
        Enabled
    );
    Steps.Control.classList.add("Steps");
    Control.appendChild(Steps.Control);

};
vDesk.MetaInformation.Mask.Row.Validator.Number.Implements(vDesk.MetaInformation.Mask.Row.IValidator);
vDesk.MetaInformation.Mask.Row.Validator.Number.Types = [Type.Int, Type.Float];
