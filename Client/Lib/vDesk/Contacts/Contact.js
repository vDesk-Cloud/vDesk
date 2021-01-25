"use strict";
/**
 * Fired if the Contact has been selected.
 * @event vDesk.Contacts.Contact#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Contacts.Contact} detail.sender The current instance of the Contact.
 */
/**
 * Fired if the Contact has been opened.
 * @event vDesk.Contacts.Contact#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.Contacts.Contact} detail.sender The current instance of the Contact.
 */
/**
 * Fired if the Contact has been right clicked on.
 * @event vDesk.Contacts.Contact#context
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'context' event.
 * @property {vDesk.Contacts.Contact} detail.sender The current instance of the Contact.
 * @property {Number} detail.x The horizontal position where the right click has appeared.
 * @property {Number} detail.y The vertical position where the right click has appeared.
 */
/**
 * Initializes a new instance of the Contact class.
 * @class Represents a viewmodel of a Contact.
 * @param {?Number} [ID=null] Initializes the Contact with the specified ID.
 * @param {?vDesk.Security.User} [Owner=null] Initializes the Contact with the specified owner.
 * @param {Number} [Gender=0] Initializes the Contact with the specified gender.
 * @param {String} [Title=""] Initializes the Contact with the specified title.
 * @param {String} [Forename=""] Initializes the Contact with the specified forename.
 * @param {String} [Surname=""] Initializes the Contact with the specified surname.
 * @param {String} [Street=""] Initializes the Contact with the specified street.
 * @param {String} [HouseNumber=""] Initializes the Contact with the specified house number.
 * @param {Number} [ZipCode=0] Initializes the Contact with the specified zip code.
 * @param {String} [City=""] Initializes the Contact with the specified city.
 * @param {vDesk.Locale.Country} [Country=null] Initializes the Contact with the specified Country.
 * @param {Array<vDesk.Contacts.Contact.Option>} [Options=[]] Initializes the Contact with the specified Options.
 * @param {vDesk.Contacts.Company} [Company=null] Initializes the Contact with the specified Company.
 * @param {String} [Annotations=""] Initializes the Contact with the specified annotations.
 * @param {vDesk.Security.AccessControlList} [AccessControlList=vDesk.Security.AccessControlList] Initializes the Contact with the specified AccessControlList.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Number} ID Gets or sets the ID of the Contact.
 * @property {vDesk.Security.User} Owner Gets or sets the owner of the Contact.
 * @property {Number} Gender Gets or sets the gender of the Contact.
 * @property {String} Title Gets or sets the title of the Contact.
 * @property {String} Forename Gets or sets the forename of the Contact.
 * @property {String} Surname Gets or sets the surname of the Contact.
 * @property {String} Street Gets or sets the street of the Contact.
 * @property {String} HouseNumber Gets or sets the housenumber of the Contact.
 * @property {Number} ZipCode Gets or sets the zipcode of the Contact.
 * @property {String} City Gets or sets the city of the Contact.
 * @property {Number} Country Gets or sets the ID of the Country of the Contact.
 * @property {Array<vDesk.Contacts.Contact.Option>} Options Gets or sets the Options of the Contact.
 * @property {vDesk.Contacts.Company} Company Gets or sets the Company of the Contact.
 * @property {String} Annotations Gets or sets the annotations of the Contact.
 * @property {vDesk.Security.AccessControlList} AccessControlList Gets or sets the AccessControlList of the Contact.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Contact is selected.
 * @augments vDesk.Controls.Table.Row
 * @memberOf vDesk.Contacts
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Contacts.Contact = function Contact(
    ID                = null,
    Owner             = vDesk.User,
    Gender            = 0,
    Title             = "",
    Forename          = "",
    Surname           = "",
    Street            = "",
    HouseNumber       = "",
    ZipCode           = 0,
    City              = "",
    Country           = new vDesk.Locale.Country(),
    Options           = [],
    Company           = new vDesk.Contacts.Company(),
    Annotations       = "",
    AccessControlList = new vDesk.Security.AccessControlList
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Owner, vDesk.Security.User, "Owner");
    Ensure.Parameter(Gender, Type.Number, "Gender");
    Ensure.Parameter(Title, Type.String, "Title");
    Ensure.Parameter(Forename, Type.String, "Forename");
    Ensure.Parameter(Surname, Type.String, "Surname");
    Ensure.Parameter(Street, Type.String, "Street");
    Ensure.Parameter(HouseNumber, Type.String, "HouseNumber");
    Ensure.Parameter(ZipCode, Type.Number, "ZipCode");
    Ensure.Parameter(City, Type.String, "City");
    Ensure.Parameter(Country, vDesk.Locale.Country, "Country", true);
    Ensure.Parameter(Options, Array, "Options");
    Ensure.Parameter(Company, vDesk.Contacts.Company, "Company", true);
    Ensure.Parameter(Annotations, Type.String, "Annotations");
    Ensure.Parameter(AccessControlList, vDesk.Security.AccessControlList, "AccessControlList");

    /**
     * The index of the Contact.
     * @type {Number}
     */
    let Index = 0;

    /**
     * Flag indicating whether the Contact is selected.
     * @type {Boolean}
     */
    let Selected = false;

    /**
     * Flag indicating whether the Contact is enabled.
     * @type {Boolean}
     */
    let Enabled = true;

    Object.defineProperties(this, {
        Control:           {
            enumerable: true,
            get:        () => Control
        },
        Enabled:           {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
            }
        },
        Index:             {
            enumerable: true,
            get:        () => Index,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Index");
                Index = Value;
            }
        },
        Selected:          {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        ID:                {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Owner:             {
            enumerable: true,
            get:        () => Owner,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "Owner", true);
                Owner = Value;
            }
        },
        Gender:            {
            enumerable: true,
            get:        () => Gender,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Gender");
                Gender = Value;
                GenderCell.textContent = Value === 0 ? vDesk.Locale.Contacts.GenderMale : vDesk.Locale.Contacts.GenderFemale;
            }
        },
        Title:             {
            enumerable: true,
            get:        () => Title,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                Title = Value;
                TitleCell.textContent = Value;
            }
        },
        Forename:          {
            enumerable: true,
            get:        () => Forename,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Forename");
                Forename = Value;
                ForenameCell.textContent = Value;
            }
        },
        Surname:           {
            enumerable: true,
            get:        () => Surname,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Surname");
                Surname = Value;
                SurnameCell.textContent = Value;
            }
        },
        Street:            {
            enumerable: true,
            get:        () => Street,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Street");
                Street = Value;
                StreetCell.textContent = Value;
            }
        },
        HouseNumber:       {
            enumerable: true,
            get:        () => HouseNumber,
            set:        Value => {
                Ensure.Property(Value, Type.String, "HouseNumber");
                HouseNumber = Value;
                HouseNumberCell.textContent = Value;
            }
        },
        ZipCode:           {
            enumerable: true,
            get:        () => ZipCode,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ZipCode");
                ZipCode = Value;
                ZipCodeCell.textContent = Value.toString();
            }
        },
        City:              {
            enumerable: true,
            get:        () => City,
            set:        Value => {
                Ensure.Property(Value, Type.String, "City");
                City = Value;
                CityCell.textContent = Value;
            }
        },
        Country:           {
            enumerable: true,
            get:        () => Country,
            set:        Value => {
                Ensure.Property(Value, vDesk.Locale.Country, "Country");
                Country = Value;
            }
        },
        Options:           {
            enumerable: true,
            get:        () => Options,
            set:        Value => {
                Ensure.Property(Value, Array, "Options");
                Options = Value;
            }
        },
        Company:           {
            enumerable: true,
            get:        () => Company,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Company", true);
                Company = Value;
            }
        },
        Annotations:       {
            enumerable: true,
            get:        () => Annotations,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Annotations");
                Annotations = Value;
            }
        },
        AccessControlList: {
            enumerable: true,
            get:        () => AccessControlList,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.AccessControlList, "AccessControlList");
                AccessControlList = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Contacts.Contact#select
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'dblclick' event.
     * @fires vDesk.Contacts.Contact#open
     */
    const OnDoubleClick = () => new vDesk.Events.BubblingEvent("open", {sender: this}).Dispatch(Control);

    /**
     * Eventhandler that listens on the 'contextmenu' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Contacts.Contact#context
     */
    const OnContextMenu = Event => {
        Event.stopPropagation();
        Event.preventDefault();
        new vDesk.Events.BubblingEvent("context", {
            sender: this,
            x:      Event.pageX,
            y:      Event.pageY
        }).Dispatch(Control);
        return false;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLTableRowElement}
     */
    const Control = document.createElement("tr");
    Control.className = "Row Contact";
    Control.addEventListener("click", OnClick, false);
    Control.addEventListener("dblclick", OnDoubleClick, false);
    Control.addEventListener("contextmenu", OnContextMenu, false);

    /**
     * The gender cell of the Contact.
     * @type {HTMLTableCellElement}
     */
    const GenderCell = document.createElement("td");
    GenderCell.className = "Cell BorderLight Font Dark";
    GenderCell.textContent = Gender === 0 ? vDesk.Locale.Contacts.GenderMale : vDesk.Locale.Contacts.GenderFemale;

    /**
     * The title cell of the Contact.
     * @type {HTMLTableCellElement}
     */
    const TitleCell = document.createElement("td");
    TitleCell.className = "Cell BorderLight Font Dark";
    TitleCell.textContent = Title;

    /**
     * The surname cell of the Contact.
     * @type {HTMLTableCellElement}
     */
    const SurnameCell = document.createElement("td");
    SurnameCell.className = "Cell BorderLight Font Dark";
    SurnameCell.textContent = Surname;

    /**
     * The forename cell of the Contact.
     * @type {HTMLTableCellElement}
     */
    const ForenameCell = document.createElement("td");
    ForenameCell.className = "Cell BorderLight Font Dark";
    ForenameCell.textContent = Forename;

    /**
     * The street cell of the Contact.
     * @type {HTMLTableCellElement}
     */
    const StreetCell = document.createElement("td");
    StreetCell.className = "Cell BorderLight Font Dark";
    StreetCell.textContent = Street;

    /**
     * The housenumber cell of the Contact.
     * @type {HTMLTableCellElement}
     */
    const HouseNumberCell = document.createElement("td");
    HouseNumberCell.className = "Cell BorderLight Font Dark";
    HouseNumberCell.textContent = HouseNumber;

    /**
     * The zipcode cell of the Contact.
     * @type {HTMLTableCellElement}
     */
    const ZipCodeCell = document.createElement("td");
    ZipCodeCell.className = "Cell BorderLight Font Dark";
    ZipCodeCell.textContent = ZipCode > 0 ? ZipCode : "";

    /**
     * The city cell of the Contact.
     * @type {HTMLTableCellElement}
     */
    const CityCell = document.createElement("td");
    CityCell.className = "Cell BorderLight Font Dark";
    CityCell.textContent = City;

    Control.appendChild(GenderCell);
    Control.appendChild(TitleCell);
    Control.appendChild(SurnameCell);
    Control.appendChild(ForenameCell);
    Control.appendChild(StreetCell);
    Control.appendChild(HouseNumberCell);
    Control.appendChild(ZipCodeCell);
    Control.appendChild(CityCell);
};

/**
 * Factory method that creates a Contact from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Contact.
 * @return {vDesk.Contacts.Contact} A Contact filled with the provided data.
 */
vDesk.Contacts.Contact.FromDataView = function(DataView) {
    return new vDesk.Contacts.Contact(
        DataView?.ID ?? null,
        vDesk.Security.Users.find(User => User.ID === DataView?.Owner?.ID) ?? vDesk.Security.User.FromDataView(DataView?.Owner ?? {}),
        DataView?.Gender ?? 0,
        DataView?.Title ?? "",
        DataView?.Forename ?? "",
        DataView?.Surname ?? "",
        DataView?.Street ?? "",
        DataView?.HouseNumber ?? "",
        DataView?.ZipCode ?? 0,
        DataView?.City ?? "",
        vDesk.Locale.Country.FromDataView(DataView?.Country ?? {}),
        DataView?.Options?.map(Option => vDesk.Contacts.Contact.Option.FromDataView(Option)) ?? [],
        vDesk.Contacts.Company.FromDataView(DataView?.Company ?? {}),
        DataView?.Annotations ?? "",
        vDesk.Security.AccessControlList.FromDataView(DataView?.AccessControlList ?? {})
    );
};

vDesk.Contacts.Contact.Implements(vDesk.Controls.Table.IRow);