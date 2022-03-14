"use strict";
/**
 * Fired if the Company has been selected.
 * @event vDesk.Contacts.Company#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Contacts.Company} detail.sender The current instance of the Company.
 */
/**
 * Fired if the Company has been opened.
 * @event vDesk.Contacts.Company#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.Contacts.Company} detail.sender The current instance of the Company.
 */
/**
 * Fired if the Company has been right clicked on.
 * @event vDesk.Contacts.Company#context
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'context' event.
 * @property {vDesk.Contacts.Company} detail.sender The current instance of the Company.
 * @property {Number} detail.x The horizontal position where the right click has appeared.
 * @property {Number} detail.y The vertical position where the right click has appeared.
 */
/**
 * Initializes a new instance of the Company class.
 * @class Represents a Company contact.
 * @param {?Number} [ID=null] Initializes the Company with the specified ID.
 * @param {String} [Name=""] Initializes the Company with the specified name.
 * @param {String} [Street=""] Initializes the Company with the specified street.
 * @param {String} [HouseNumber=""] Initializes the Company with the specified house number.
 * @param {Number} [ZipCode=0] Initializes the Company with the specified zip code.
 * @param {String} [City=""] Initializes the Company with the specified city.
 * @param {vDesk.Locale.Country} [Country=null] Initializes the Company with the specified Country.
 * @param {String} [PhoneNumber=""] Initializes the Company with the specified phone number
 * @param {String} [FaxNumber=""] Initializes the Company with the specified fax number.
 * @param {String} [Email=""] Initializes the Company with the specified email address.
 * @param {String} [Website=""] Initializes the Company with the specified website.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Company is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {?Number} ID Gets or sets the ID of the Company.
 * @property {String} Name Gets or sets the name of the Company.
 * @property {String} Street Gets or sets the street of the Company.
 * @property {String} HouseNumber Gets or sets the house number of the Company.
 * @property {Number} ZipCode Gets or sets the zip code of the Company.
 * @property {String} City Gets or sets the city of the Company.
 * @property {Number} Country Gets or sets the ID of the country of the Company.
 * @property {String} PhoneNumber Gets or sets the phone number of the Company.
 * @property {String} FaxNumber Gets or sets the fax number of the Company.
 * @property {String} Email Gets or sets the email address of the Company.
 * @property {String} Website Gets or sets the website of the Company.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Company is selected.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Company is  enabled.
 * @implements vDesk.Controls.Table.IRow
 * @memberOf vDesk.Contacts
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 */
vDesk.Contacts.Company = function Company(
    ID          = null,
    Name        = "",
    Street      = "",
    HouseNumber = "",
    ZipCode     = 0,
    City        = "",
    Country     = new vDesk.Locale.Country(),
    PhoneNumber = "",
    FaxNumber   = "",
    Email       = "",
    Website     = "",
    Enabled     = true
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Name, Type.String, "Name");
    Ensure.Parameter(Street, Type.String, "Street");
    Ensure.Parameter(HouseNumber, Type.String, "HouseNumber");
    Ensure.Parameter(ZipCode, Type.Number, "ZipCode");
    Ensure.Parameter(City, Type.String, "City");
    Ensure.Parameter(Country, vDesk.Locale.Country, "Country");
    Ensure.Parameter(PhoneNumber, Type.String, "PhoneNumber");
    Ensure.Parameter(FaxNumber, Type.String, "FaxNumber");
    Ensure.Parameter(Email, Type.String, "Email");
    Ensure.Parameter(Website, Type.String, "Website");

    /**
     * The index of the Company.
     * @type {Number}
     */
    let Index = 0;

    /**
     * Flag indicating whether the contact is selected.
     * @type {Boolean}
     */
    let Selected = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },

        Index:       {
            enumerable: true,
            get:        () => Index,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Index");
                Index = Value;
            }
        },
        Selected:    {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        ID:          {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Name:        {
            enumerable: true,
            get:        () => Name,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                Name = value;
                NameCell.textContent = Name;
            }
        },
        Street:      {
            enumerable: true,
            get:        () => Street,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Street");
                Street = Value;
                StreetCell.textContent = Value;
            }
        },
        HouseNumber: {
            enumerable: true,
            get:        () => HouseNumber,
            set:        Value => {
                Ensure.Property(Value, Type.String, "HouseNumber");
                HouseNumber = Value;
                HouseNumberCell.textContent = Value;
            }
        },
        ZipCode:     {
            enumerable: true,
            get:        () => ZipCode,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ZipCode");
                ZipCode = Value;
                ZipCodeCell.textContent = Value.toString();
            }
        },
        City:        {
            enumerable: true,
            get:        () => City,
            set:        Value => {
                Ensure.Property(Value, Type.String, "City");
                City = Value;
                CityCell.textContent = Value;
            }
        },
        Country:     {
            enumerable: true,
            get:        () => Country,
            set:        Value => {
                Ensure.Property(Value, vDesk.Locale.Country, "Country");
                Country = Value;
            }
        },
        PhoneNumber: {
            enumerable: true,
            get:        () => PhoneNumber,
            set:        Value => {
                Ensure.Property(Value, Type.String, "PhoneNumber");
                PhoneNumber = value;
            }
        },
        FaxNumber:   {
            enumerable: true,
            get:        () => FaxNumber,
            set:        Value => {
                Ensure.Property(Value, Type.String, "FaxNumber");
                FaxNumber = value;
            }
        },
        Email:       {
            enumerable: true,
            get:        () => Email,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Email");
                Email = value;
            }
        },
        Website:     {
            enumerable: true,
            get:        () => Website,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Website");
                Website = value;
            }
        },
        Enabled:     {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Contacts.Company#select
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'dblclick' event.
     * @fires vDesk.Contacts.Company#open
     */
    const OnDoubleClick = () => {
        new vDesk.Events.BubblingEvent("open", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'contextmenu' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Contacts.Company#context
     */
    const OnContextMenu = Event => {
        Event.stopPropagation();
        Event.preventDefault();
        new vDesk.Events.BubblingEvent("context", {
            sender: this,
            x:      Event.pageX,
            y:      Event.pageY
        }).Dispatch(Control);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLTableRowElement}
     */
    const Control = document.createElement("tr");
    Control.className = "Row Company";
    Control.addEventListener("click", OnClick, false);
    Control.addEventListener("dblclick", OnDoubleClick, false);
    Control.addEventListener("contextmenu", OnContextMenu, false);

    /**
     * The name cell of the Company.
     * @type {HTMLTableCellElement}
     */
    const NameCell = document.createElement("td");
    NameCell.className = "Cell BorderLight Font Dark";
    NameCell.textContent = Name;

    /**
     * The street cell of the Company.
     * @type {HTMLTableCellElement}
     */
    const StreetCell = document.createElement("td");
    StreetCell.className = "Cell BorderLight Font Dark";
    StreetCell.textContent = Street;

    /**
     * The house number cell of the Company.
     * @type {HTMLTableCellElement}
     */
    const HouseNumberCell = document.createElement("td");
    HouseNumberCell.className = "Cell BorderLight Font Dark";
    HouseNumberCell.textContent = HouseNumber;

    /**
     * The zip code cell of the Company.
     * @type {HTMLTableCellElement}
     */
    const ZipCodeCell = document.createElement("td");
    ZipCodeCell.className = "Cell BorderLight Font Dark";
    ZipCodeCell.textContent = (ZipCode > 0) ? ZipCode : "";

    /**
     * The city cell of the Company.
     * @type {HTMLTableCellElement}
     */
    const CityCell = document.createElement("td");
    CityCell.className = "Cell BorderLight Font Dark";
    CityCell.textContent = City;

    Control.appendChild(NameCell);
    Control.appendChild(StreetCell);
    Control.appendChild(HouseNumberCell);
    Control.appendChild(ZipCodeCell);
    Control.appendChild(CityCell);
};

/**
 * Factory method that creates a Company from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Company.
 * @return {vDesk.Contacts.Company} A Company filled with the provided data.
 */
vDesk.Contacts.Company.FromDataView = function(DataView) {
    return new vDesk.Contacts.Company(
        DataView?.ID ?? null,
        DataView?.Name ?? "",
        DataView?.Street ?? "",
        DataView?.HouseNumber ?? "",
        DataView?.ZipCode ?? 0,
        DataView?.City ?? "",
        vDesk.Locale.Country.FromDataView(DataView?.Country ?? {}),
        DataView?.PhoneNumber ?? "",
        DataView?.FaxNumber ?? "",
        DataView?.Email ?? "",
        DataView?.Website ?? ""
    );
};

vDesk.Contacts.Company.Implements(vDesk.Controls.Table.IRow);