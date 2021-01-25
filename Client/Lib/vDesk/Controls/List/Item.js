/**
 *
 * @constructor
 */
vDesk.Controls.List.Item = function Item(Value, Draggable = false, Enabled = true) {

    Ensure.Parameter(Draggable, Type.Boolean, "Draggable");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

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
        Value:    {
            enumerable: true,
            get:        () => Value,
            set:        NewValue => {
                Value = NewValue;
                Control.textContent = NewValue;
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Enabled:  {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");

                Enabled = Value;

                if(Value) {
                    Control.addEventListener("click", OnClick, false);
                } else {
                    Control.removeEventListener("click", OnClick, false);
                }

                Control.classList.toggle("Disabled", !Value);
                Control.draggable = Value;
                Control.style.cursor = Value ? "grab" : "pointer";
            }
        }
    });

    const Control = document.createElement("li");
    Control.className = "Item";
    Control.textContent = Value;

}