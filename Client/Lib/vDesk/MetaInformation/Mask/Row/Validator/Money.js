"use strict";
/**
 * Fired if the values of the IValidator has been updated.
 * @event vDesk.MetaInformation.Mask.Row.Validator.Money#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.MetaInformation.Mask.Row.Validator.Money} detail.sender The current instance of the IValidator.
 */
/**
 * Initializes a new instance of the Money class.
 * @class Represents a currency based numeric validator.
 * @memberOf vDesk.MetaInformation.Mask.Row.Validator
 * @implements vDesk.MetaInformation.Mask.Row.IValidator
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.Mask.Row.Validator.Money = function Money(Validator = null, Enabled = true) {
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
        Currency:  {
            enumerable: true,
            get:        () => Currency.Value,
            set:        Value => Currency.Value = Value
        },
        Validator: {
            enumerable: true,
            get:        () => {
                return {
                    Min: Min.Value,
                    Max: Max.Value,
                    Currency: Currency.Value
                };
            },
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Validator", true);
                Min.Value = Value?.Min ?? null;
                Max.Value = Value?.Max ?? window.Number.MAX_SAFE_INTEGER;
                Currency.Value = Value?.Currency ?? null;
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
                Currency.Enabled = Value;
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
    Control.className = "Validator Money";
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
     * The currency EditControl of the Money validator.
     * @type {vDesk.Controls.EditControl}
     */
    const Currency = new vDesk.Controls.EditControl(
        `${vDesk.Locale.MetaInformation.Currency}:`,
        null,
        Extension.Type.Enum,
        Validator?.Currency ?? null,
        [
            "€",
            "$",
            "£",
            "¥",
            "₽",
            "₿"
        ],
        true,
        Enabled
    );
    Currency.Control.classList.add("Currency");
    Control.appendChild(Currency.Control);

};
vDesk.MetaInformation.Mask.Row.Validator.Money.Implements(vDesk.MetaInformation.Mask.Row.IValidator);
vDesk.MetaInformation.Mask.Row.Validator.Money.Types = [Extension.Type.Money];