"use strict";
/**
 * Fired if the value of the SuggestionTextBox has been updated.
 * @event vDesk.Controls.SuggestionTextBox#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.SuggestionTextBox} detail.sender The current instance of the SuggestionTextBox.
 * @property {String} detail.value The current value of the SuggestionTextBox.
 */
/**
 * Fired if the value of the SuggestionTextBox has been cleared.
 * @event vDesk.Controls.SuggestionTextBox#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.SuggestionTextBox} detail.sender The current instance of the SuggestionTextBox.
 */
/**
 * Initializes a new instance of the SuggestionTextBox class.
 * @class Represents a TextBox capable of displaying a set of predefined values and matching the entered text.
 * @param {*} [Value=null] Initializes the SuggestionTextBox with the specified value.
 * @param {Array<String>} [Cases=[]] Initializes the SuggestionTextBox with the specified suggestions.
 * @param {Boolean} [Enabled=true] Flag indicating whether the SuggestionTextBox is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Value Gets or sets the value of the SuggestionTextBox.
 * @property {Array<String>} Suggestions Gets or sets the suggestions of the SuggestionTextBox.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the SuggestionTextBox is enabled.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Switch = function Switch(Value = null, Cases = {}, Enabled = true) {
    Ensure.Parameter(Cases, Type.Object, "Cases");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");


    Object.defineProperties(this, {
        Control:     {
            enumerable: true,
            get:        () => Control
        },
        Value:       {
            enumerable: true,
            get:        () => TextBox.value,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Value");
                TextBox.value = Value;
            }
        },
        Suggestions: {
            enumerable: true,
            get:        () => Suggestions,
            set:        Value => {
                Ensure.Property(Value, [Array, Type.Object], "Suggestions");

                //Clear suggestion list.
                while(DropDownList.hasChildNodes()) {
                    DropDownList.removeChild(DropDownList.lastChild);
                }

                //Clear array.
                Suggestions = Value;

                //Append new rows.
                const Fragment = document.createDocumentFragment();
                Items = Value.map(Suggestion => {
                    const ListItem = document.createElement("li");
                    ListItem.textContent = Suggestion;
                    ListItem.style.display = "none";
                    ListItem.className = "Item Font";
                    Fragment.appendChild(ListItem);
                    return ListItem;
                });

                DropDownList.appendChild(Fragment);
            }
        },
        Enabled:     {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                DropDownButton.disabled = !Value;
                TextBox.disabled = !Value;
                if(!Value && Expanded) {
                    window.requestAnimationFrame(Collapse);
                }
            }
        }
    });



    /**
     * Eventhandler that listens on the 'click' event on a listitem.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.SuggestionTextBox#update
     */
    const OnClickListItem = Event => {
        Event.stopPropagation();
        TextBox.value = Event.target.textContent;
        Collapse();
        Matches = [];
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            value:  Event.target.textContent
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Controls.SuggestionTextBox#update
     */
    const OnClick = () => {
        window.requestAnimationFrame(Collapse);
        if(SelectedIndex > -1) {
            new vDesk.Events.BubblingEvent("update", {
                sender: this,
                value:  TextBox.value
            }).Dispatch(Control);
        }
    };



    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Switch";

    let Controls = [];

    Cases.forEach((Key, Val) =>{
        const Case = document.createElement("input");
        Case.type = "radio";
        Case.addEventListener("click", () => Value = Val);
        Control.appendChild(Case);
    });


};