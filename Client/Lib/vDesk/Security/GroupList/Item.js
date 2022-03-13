"use strict";
/**
 * Fired if the Item has been selected.
 *
 * @event vDesk.Security.GroupList.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Security.GroupList.Item} detail.sender The current instance of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 *
 * @class Represents an item inside a GroupList.
 * @param {vDesk.Security.Group} Group Initializes the Item with the specified Group.
 * @param {Boolean} [Draggable=false] Flag indicating whether the Item is draggable.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Item is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.Group} Group Gets or sets the Group of the Item.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Item is selected.
 * @property {Boolean} Draggable Gets or sets a value indicating whether the Item is draggable.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Item is enabled.
 * @memberOf vDesk.Security.GroupList
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Security
 */
vDesk.Security.GroupList.Item = function Item(Group, Draggable = false, Enabled = true) {
    Ensure.Parameter(Group, vDesk.Security.Group, "Group");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the Item is selected.
     * @type {Boolean}
     */
    let Selected = false;

    Object.defineProperties(this, {
        Control:   {
            enumerable: true,
            get:        () => Control
        },
        Group:     {
            enumerable: true,
            get:        () => Group,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.Group, "Group");
                Group = Value;
                Control.textContent = Group.Name;
            }
        },
        Selected:  {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Draggable: {
            enumerable: true,
            get:        () => Draggable,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Draggable");
                Draggable = Value;
                Control.classList.toggle("Selected", Value);
                if(Group.ID !== vDesk.Security.Group.Everyone){
                    Control.draggable = Value;
                    Control.style.cursor = Value ? "grab" : "pointer";

                }
            }
        },
        Enabled:   {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Control.classList.toggle("Disabled", !Value);
                if(Value){
                    Control.addEventListener("click", OnClick, false);
                    Control.addEventListener("dragstart", OnDragStart, false);
                    Control.addEventListener("dragend", OnDragEnd, false);
                }else{
                    Control.removeEventListener("click", OnClick, false);
                    Control.removeEventListener("dragstart", OnDragStart, false);
                    Control.removeEventListener("dragend", OnDragEnd, false);
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     *
     * @fires vDesk.Security.GroupList.Item#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("select", {
        sender: this,
        group:  Group
    }).Dispatch(Control);

    /**
     * Eventhandler that listens on the 'dragstart' event.
     *
     * @param {DragEvent} Event
     */
    const OnDragStart = Event => {
        Control.style.cursor = "grabbing";
        Event.dataTransfer.effectAllowed = "move";
        Event.dataTransfer.setReference(this);
        return false;
    };

    /**
     * Eventhandler that listens on the 'dragend' event.
     */
    const OnDragEnd = () => Control.style.cursor = "grab";

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item Font Dark BorderLight";
    Control.textContent = Group.Name;
    Control.style.cursor = "pointer";
    Control.classList.toggle("Disabled", !Enabled);
    Control.classList.toggle("Selected", Selected);
    if(Group.ID !== vDesk.Security.Group.Everyone){
        Control.draggable = Draggable;
        Control.style.cursor = Draggable ? "grab" : "pointer";
    }
    if(Enabled){
        Control.addEventListener("click", OnClick, false);
        Control.addEventListener("dragstart", OnDragStart, false);
        Control.addEventListener("dragend", OnDragEnd, false);
    }
};