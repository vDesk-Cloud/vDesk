"use strict";
/**
 * Initializes a new instance of the Item class.
 * @class Represents a menu-Item within the Menu of the client.
 * @param {String} [Name=""] The name of the Item.
 * @param {String} [Icon=""] The icon of the Item.
 * @param {Function} [Callback=null] The callback of the Item to execute on a click event.
 * @property {HTMLElement} Controls Gets the underlying DOM-Node.
 * @property {String} Name Gets or sets the name of the Item.
 * @property {String} Icon Gets or sets the icon of the Item.
 * @property {Function} Callback Gets or sets the callback of the Item.
 * @memberOf vDesk.Menu
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Menu.Item = function Item(Name = "", Icon = "", Callback = null) {
    Ensure.Parameter(Name, Type.String, "Name");
    Ensure.Parameter(Icon, Type.String, "Icon");
    Ensure.Parameter(Callback, Type.Function, "Callback", true);

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Name:     {
            enumerable: true,
            get:        () => NameSpan.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                NameSpan.textContent = Value;
            }
        },
        Icon:     {
            enumerable: true,
            get:        () => IconImage.src,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Icon");
                IconImage.src = Value;
            }
        },
        Callback: {
            enumerable: true,
            get:        () => Callback,
            set:        Value => {
                Ensure.Property(Value, Type.Function, "Callback", true);
                Callback = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * Executes the callback of the Item.
     */
    const OnClick = Event => {
        Event.stopPropagation();
        if(Callback !== null) {
            Callback();
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item";
    Control.addEventListener("click", OnClick, false);

    /**
     * The icon of the Item.
     * @type {HTMLImageElement}
     */
    const IconImage = document.createElement("img");
    IconImage.className = "Icon";
    IconImage.src = Icon;

    /**
     * The name span of the Item.
     * @type {HTMLSpanElement}
     */
    const NameSpan = document.createElement("span");
    NameSpan.className = "Name Font Dark";
    NameSpan.textContent = Name;

    Control.appendChild(IconImage);
    Control.appendChild(NameSpan);
};