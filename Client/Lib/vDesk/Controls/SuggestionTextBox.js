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
 * @param {String} [Value=""] Initializes the SuggestionTextBox with the specified value.
 * @param {Array<String>} [Suggestions=[]] Initializes the SuggestionTextBox with the specified suggestions.
 * @param {Boolean} [Enabled=true] Flag indicating whether the SuggestionTextBox is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Value Gets or sets the value of the SuggestionTextBox.
 * @property {Array<String>} Suggestions Gets or sets the suggestions of the SuggestionTextBox.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the SuggestionTextBox is enabled.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.SuggestionTextBox = function SuggestionTextBox(Value = "", Suggestions = [], Enabled = true) {
    Ensure.Parameter(Value, Type.String, "InitialValue");
    Ensure.Parameter(Suggestions, [Array, Type.Object], "Suggestions");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The collection of matching items of the SuggestionTextBox.
     * @type {Array<HTMLLIElement>}
     */
    let Matches = [];

    /**
     * The index of the current selected listitem of the SuggestionTextBox.
     * @type {Number}
     */
    let SelectedIndex = -1;

    /**
     * The value of the last entered text.
     * @type {String}
     */
    let OriginalValue = null;

    /**
     * Flag indicating whether the dropdownlist of the SuggestionTextBox is expanded.
     * @type {Boolean}
     */
    let Expanded = false;

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
     * Eventhandler that listens on the 'input' event and displays matching suggestions.
     * @fires vDesk.Controls.SuggestionTextBox#update
     */
    const OnInput = () => {
        if(TextBox.value.length > 0) {
            OriginalValue = TextBox.value;
            SelectedIndex = -1;
            //Filter and display matching listitems.
            Matches = Items.filter(Item => {
                if(Item.textContent.match(new RegExp(TextBox.value, "i"))) {
                    Item.style.display = "list-item";
                    return true;
                }
                Item.style.display = "none";
                return false;
            });

            if(Matches.length > 0) {
                DropDownList.style.display = "block";
                Expanded = true;
            } else {
                DropDownList.style.display = "none";
                Expanded = false;
            }

            //Enabled/disable navigation through suggestion list-items.
            if(Expanded) {
                DropDownButton.textContent = "▲";
                window.addEventListener("keydown", OnKeyDown, false);
            } else {
                DropDownButton.textContent = "▼";
                window.removeEventListener("keydown", OnKeyDown, false);
            }
            new vDesk.Events.BubblingEvent("update", {
                sender: this,
                value:  TextBox.value
            }).Dispatch(Control);
        } else {
            new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
            DropDownList.style.display = "none";
            DropDownButton.textContent = "▼";
            Expanded = false;
        }

    };

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
     * Eventhandler that listens on the 'click' event and toggles the visibility of the SuggestionTextBox.
     */
    const OnClickButton = () => {
        if(Expanded) {
            window.requestAnimationFrame(Collapse);
        } else {
            window.requestAnimationFrame(Expand);
        }
    };

    /**
     * Eventhandler that listens on the 'keydown' event and navigates through suggestions or submits the current selected suggestion.
     * @param {KeyboardEvent} Event
     * @fires vDesk.Controls.SuggestionTextBox#update
     */
    const OnKeyDown = Event => {
        if(Expanded) {
            switch(Event.key) {
                case "ArrowUp":
                    //Check if the pointer has not reached -1.
                    if(SelectedIndex > -1) {

                        //Check if the pointer has reached 0 and use the original entered value.
                        if(SelectedIndex === 0) {
                            TextBox.value = OriginalValue;
                            Matches[SelectedIndex--].className = "Item Font";
                        }
                        //Otherwise highlight and select the next suggestion list-item
                        else {
                            Matches[SelectedIndex].className = "Item Font";
                            TextBox.value = Matches[--SelectedIndex].textContent;
                            Matches[SelectedIndex].className = "Item Font Selected";
                        }
                    }
                    break;
                case "ArrowDown":
                    //Check if the pointer has not reached the limit of matched suggestions.
                    if(SelectedIndex < Matches.length - 1) {
                        //Check if the pointer is above 0 and reset the previous suggestion list-item. 
                        if(SelectedIndex >= 0) {
                            Matches[SelectedIndex].className = "Item Font";
                        }
                        //Highlight and select the next suggestion list-item.
                        TextBox.value = Matches[++SelectedIndex].textContent;
                        Matches[SelectedIndex].className = "Item Font Selected";
                    }
                    break;
                case "Enter":
                    //Collapse the suggestion dropdown-list.
                    window.requestAnimationFrame(Collapse);
                    Matches.splice(0, Matches.length);
                    if(SelectedIndex > -1) {
                        new vDesk.Events.BubblingEvent("update", {
                            sender: this,
                            value:  TextBox.value
                        }).Dispatch(Control);
                    }
                    break;
            }
        }
    };

    /**
     * Expands the dropdownlist.
     */
    const Expand = function() {
        Items.forEach(Item => Item.style.display = "list-item");
        DropDownList.style.display = "block";
        Expanded = true;
        DropDownButton.textContent = "▲";
    };

    /**
     * Collapses the dropdownlist.
     */
    const Collapse = function() {
        Items.forEach(Item => {
            Item.style.display = "none";
            Item.className = "Item Font";
        });
        DropDownList.style.display = "none";
        Expanded = false;
        DropDownButton.textContent = "▼";
    };

    /**
     * Expands the set of matching suggestions.
     */
    this.Expand = function() {
        if(!Expanded) {
            window.requestAnimationFrame(Expand);
        }
    };
    /**
     * Collapses the set of matching suggestions.
     */
    this.Collapse = function() {
        if(Expanded) {
            window.requestAnimationFrame(Collapse);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "SuggestionTextBox";

    /**
     * The textbox of the SuggestionTextBox.
     * @type {HTMLInputElement}
     */
    const TextBox = document.createElement("input");
    TextBox.className = "TextBox";
    TextBox.type = "text";
    TextBox.addEventListener("input", OnInput, false);
    TextBox.value = Value ?? "";

    /**
     * The dropdown button of the SuggestionTextBox.
     * @type {HTMLButtonElement}
     */
    const DropDownButton = document.createElement("button");
    DropDownButton.className = "Button";
    DropDownButton.textContent = "▼";
    DropDownButton.addEventListener("click", OnClickButton, false);

    /**
     * The dropdown list of the SuggestionTextBox.
     * @type {HTMLUListElement}
     */
    const DropDownList = document.createElement("ul");
    DropDownList.className = "List BorderDark Background";
    DropDownList.addEventListener("click", OnClickListItem, false);
    DropDownList.style.display = "none";

    /**
     * The suggestion items of the SuggestionTextBox.
     * @type {Array<HTMLLIElement>}
     * @todo Determine between Array or Object like in the Enum EditControl.
     */
    let Items = Suggestions.map(Suggestion => {
        const ListItem = document.createElement("li");
        ListItem.textContent = Suggestion;
        ListItem.style.display = "none";
        ListItem.className = "Item Font";
        DropDownList.appendChild(ListItem);
        return ListItem;
    });

    Control.appendChild(TextBox);
    Control.appendChild(DropDownButton);
    Control.appendChild(DropDownList);
};