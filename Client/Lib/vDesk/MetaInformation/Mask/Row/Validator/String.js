"use strict";
/**
 * Fired if the values of the IValidator has been updated.
 * @event vDesk.MetaInformation.Mask.Row.Validator.String#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.MetaInformation.Mask.Row.Validator.String} detail.sender The current instance of the IValidator.
 */
/**
 * Initializes a new instance of the String class.
 * @class Represents a string validator.
 * @implements vDesk.MetaInformation.Mask.Row.IValidator
 * @memberOf vDesk.MetaInformation.Mask.Row.Validator
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.Mask.Row.Validator.String = function String(Validator = null, Enabled = true) {
    Ensure.Parameter(Validator, Type.Object, "Validator", true);
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:    {
            enumerable: true,
            get:        () => Control
        },
        Expression: {
            enumerable: true,
            get:        () => Validator?.Expression ?? null,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Expression", true);
                Validator.Expression = Value;
                Expression.Value = Value;
            }
        },
        Validator:  {
            enumerable: true,
            get:        () => Expression.Value === null
                              ? null
                              : {Expression: Expression.Value},
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Validator", true);
                Expression.Value = Value?.Expression ?? null;
            }
        },
        Enabled:    {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => Expression.Enabled = Value
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
    Control.className = "Validator String";
    Control.addEventListener("update", OnUpdate, {once: true});

    /**
     * The expression EditControl of the Money validator.
     * @type {vDesk.Controls.EditControl}
     */
    const Expression = new vDesk.Controls.EditControl(
        `${vDesk.Locale.MetaInformation.Pattern}:`,
        null,
        Type.String,
        Validator?.Expression ?? null,
        null,
        false,
        Enabled
    );
    Expression.Control.classList.add("Expression");
    Control.appendChild(Expression.Control);

};
vDesk.MetaInformation.Mask.Row.Validator.String.Implements(vDesk.MetaInformation.Mask.Row.IValidator);
vDesk.MetaInformation.Mask.Row.Validator.String.Types = [Type.String];