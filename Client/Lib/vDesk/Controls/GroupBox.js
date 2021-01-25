"use strict";
/**
 * Initializes a new instance of the GroupBox class.
 * @class Represents a GroupBox content control.
 * @param {String} Title The title of the GroupBox.
 * @param {Array<HTMLElement|DocumentFragment>} [Items=[]] The childcontrols of the GroupBox.
 * @param {Boolean} [Expandable=false] Flag indicating whether the GroupBox is expandable.
 * @param {Boolean} [Expanded=false] Flag indicating whether the GroupBox is expanded.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets or sets the text of the title of the GroupBox.
 * @property {Array<HTMLElement|DocumentFragment>} Items Gets or sets the child controls of the GroupBox.
 * @property {HTMLDivElement} Content Gets content Node of the GroupBox.
 * @property {Boolean} Expandable Gets or sets a value indicating whether the GroupBox is expandable.
 * @property {Boolean} Expanded Gets or sets a value indicating whether the GroupBox is expanded.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.GroupBox = function GroupBox(Title = "", Items = [], Expandable = false, Expanded = false) {
    Ensure.Parameter(Title, Type.String, "Title");
    Ensure.Parameter(Items, Array, "Items");
    Ensure.Parameter(Expandable, Type.Boolean, "Expandable");
    Ensure.Parameter(Expanded, Type.Boolean, "Expandable");

    Object.defineProperties(this, {
        Control:    {
            enumerable: true,
            get:        () => Control
        },
        Title:      {
            enumerable: true,
            get:        () => Header.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Header");
                Header.textContent = Value;
            }
        },
        Content:    {
            enumerable: true,
            get:        () => Content
        },
        Items:      {
            enumerable: true,
            get:        () => Items,
            set:        Value => {
                Ensure.Property(Value, Array, "Items");

                //Remove elements.
                Items.forEach(Item => Content.removeChild(Item));

                //Clear array
                Items = Value;

                //Append new elements.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Item => {
                    Ensure.Parameter(Item, [HTMLElement, DocumentFragment], "Item");
                    Fragment.appendChild(Item);
                });
                Content.appendChild(Fragment);
            }
        },
        Expandable: {
            enumerable: true,
            get:        () => Expander.style.display === "inline-block",
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Expandable");
                if(Value) {
                    Expander.style.display = "inline-block";
                } else {
                    Expander.style.display = "none";
                }
            }
        },
        Expanded:   {
            enumerable: true,
            get:        () => Content.style.display !== "none",
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Expandable");
                if(Value) {
                    Content.style.display = "block";
                    Expander.textContent = "▲";
                } else {
                    Content.style.display = "none";
                    Expander.textContent = "▼";
                }
            }
        }
    });

    /**
     * Adds a control to the GroupBox.
     * @param {HTMLElement|DocumentFragment} Control The control to add.
     */
    this.Add = function(Control) {
        Ensure.Parameter(Control, [HTMLElement, DocumentFragment], "Control");
        Items.push(Control);
        Content.appendChild(Control);
    };

    /**
     * Removes a control from the GroupBox.
     * @param {HTMLElement} Control The control to remove.
     */
    this.Remove = function(Control) {
        Ensure.Parameter(Control, [HTMLElement, DocumentFragment], "Control");
        Items.splice(Items.indexOf(Control), 1);
        Content.removeChild(Control);
    };

    /**
     * Removes all controls from the GroupBox.
     */
    this.Clear = function() {
        Items.forEach(Item => Content.removeChild(Item));
        Items = [];
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "GroupBox BorderLight";

    /**
     * The header of the GroupBox.
     * @type {HTMLSpanElement}
     */
    const Header = document.createElement("span");
    Header.className = "Header BorderLight Background Font";
    Header.textContent = Title;
    Control.appendChild(Header);

    /**
     * The expander button of the GroupBox.
     * @type {HTMLButtonElement}
     */
    const Expander = document.createElement("button");
    Expander.className = "Button Arrow Expander";
    Expander.textContent = "🡱🡳";
    this.Expandable = Expandable;
    Expander.addEventListener("click", () => this.Expanded = !this.Expanded);
    Control.appendChild(Expander);

    /**
     * The content of the GroupBox.
     * @type {HTMLDivElement}
     */
    const Content = document.createElement("div");
    Content.className = "Content Font Dark";
    Items.forEach(Item => {
        Ensure.Parameter(Item, [HTMLElement, DocumentFragment], "Item");
        Content.appendChild(Item);
    });
    if(Expandable) {
        this.Expanded = Expanded;
    }
    Control.appendChild(Content);

};