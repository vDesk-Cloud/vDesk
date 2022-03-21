"use strict";
/**
 * Fired if the Item has been selected.
 * @event vDesk.Packages.PackageList.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Packages.PackageList.Item} detail.sender The current instance of the Item.
 * @property {vDesk.Packages.Package} detail.user The Package of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 * @class Represents an Item inside the PackageList.
 * @param {vDesk.Packages.Package} Package Initializes the Item with the specified Package.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Item is enabled.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Packages.Package} Package Gets or sets the Package of the Item.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Item is enabled.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Item is selected.
 * @memberOf vDesk.Packages.PackageList
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Packages
 */
vDesk.Packages.PackageList.Item = function Item(Package, Enabled = true) {
    Ensure.Parameter(Package, vDesk.Packages.Package, "Package");
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
        Package:  {
            enumerable: true,
            get:        () => Package,
            set:        Value => {
                Ensure.Property(Value, vDesk.Packages.Package, "Package");
                Package = Value;
                Name.textContent = Value.Name;
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

                if(Value){
                    Control.addEventListener("click", OnClick, false);
                }else{
                    Control.removeEventListener("click", OnClick, false);
                }

                Control.classList.toggle("Disabled", !Value);
                Control.draggable = Value;
                Control.style.cursor = Value ? "grab" : "pointer";
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Packages.PackageList.Item#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("select", {
        sender:  this,
        package: Package
    }).Dispatch(Control);

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item Font Dark BorderLight";
    Control.classList.toggle("Disabled", !Enabled);
    Control.draggable = Enabled;
    Control.style.cursor = Enabled ? "grab" : "pointer";
    if(Enabled){
        Control.addEventListener("click", OnClick, false);
    }

    /**
     * The name span of the Item.
     * @type {HTMLSpanElement}
     */
    const Name = document.createElement("span");
    Name.textContent = Package.Name;
    Control.appendChild(Name);
};