"use strict";
/**
 * Initializes a new instance of the MainMenu class.
 * @class Represents the main-MainMenu of the Client.
 * @param {Array<vDesk.MainMenu.Item>} [Items=[]] The Items of the MainMenu.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.MainMenu.Item>} Items Gets or sets the Items of the MainMenu.
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MainMenu = function MainMenu(Items = []) {
    Ensure.Parameter(Items, Array, "Items");

    /**
     * Flag indicating whether the MainMenu is visible.
     * @type {Boolean}
     */
    let Visible = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Items:   {
            enumerable: true,
            get:        () => Items,
            set:        Value => {
                Ensure.Property(Value, Array, "Items");
                this.Clear();
                Items = Value;
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.MainMenu.Item, "Item");
                    List.appendChild(Item.Control);
                    Item.Control.addEventListener("mouseenter", () => Description.textContent = Item.Description, false);
                });
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClick = () => this.Toggle();

    /**
     * Eventhandler that listens on the 'mouseleave' event.
     */
    const OnMouseLeave = () => this.Collapse();

    /**
     * Expands the MainMenu and its Items.
     */
    this.Expand = function() {
        Container.style.display = "block";
        Visible = true;
    };

    /**
     * Collapses the MainMenu and its Items.
     */
    this.Collapse = function() {
        Container.style.display = "none";
        Visible = false;
    };

    /**
     * Toggles the visibility of the MainMenu and its Items.
     */
    this.Toggle = function() {
        if(Visible) {
            this.Collapse();
        } else {
            this.Expand();
        }
    };

    /**
     * Adds an Item to the MainMenu.
     * @param {vDesk.MainMenu.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.MainMenu.Item, "Item");
        List.appendChild(Item.Control);
        Item.Control.addEventListener("mouseenter", () => Description.textContent = Item.Description, false);
        Items.push(Item);
    };

    /**
     * Removes an Item from the MainMenu.
     * @param {vDesk.MainMenu.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.MainMenu.Item, "Item");
        const Index = Items.indexOf(Item);
        if(~Index) {
            List.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * Clears the MainMenu.
     */
    this.Clear = function() {
        Items.forEach(Item => List.removeChild(Item.Control));
        Items = [];
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "MainMenu";

    /**
     * The button of the MainMenu.
     * @type {HTMLButtonElement}
     */
    const Button = document.createElement("button");
    Button.className = "Foreground Font Light";
    Button.textContent = "vDesk";
    Button.addEventListener("click", OnClick, false);

    /**
     * The container of the MainMenu.
     * @type {HTMLDivElement}
     */
    const Container = document.createElement("div");
    Container.className = "Container Foreground";
    Container.style.display = "none";
    Container.addEventListener("mouseleave", OnMouseLeave, false);

    /**
     * The list of the MainMenu.
     * @type {HTMLUListElement}
     */
    const List = document.createElement("ul");
    List.className = "List Background";

    /**
     * The description of the MainMenu.
     * @type {HTMLDivElement}
     */
    const Description = document.createElement("div");
    Description.className = "Description Background Font Dark";

    Container.appendChild(List);
    Container.appendChild(Description);

    Control.appendChild(Button);
    Control.appendChild(Container);

    //Append passed Items to the list.
    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.MainMenu.Item, "Item");
        List.appendChild(Item.Control);
        Item.Control.addEventListener("mouseenter", () => Description.textContent = Item.Description, false);
    });
};