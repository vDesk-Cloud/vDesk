"use strict";
/**
 * Fired if the data of the Company of the Editor has been modified.
 * @event vDesk.Contacts.Company.Editor#change
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'change' event.
 * @property {vDesk.Contacts.Company.Editor} detail.sender The current instance of the Editor.
 */
/**
 * Fired if a new Company has been created.
 * @event vDesk.Contacts.Company.Editor#create
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'create' event.
 * @property {vDesk.Contacts.Company.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Contacts.Company} detail.company The newly created Company.
 */
/**
 * Fired if the current edited Company of the Editor has been updated.
 * @event vDesk.Contacts.Company.Editor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Contacts.Company.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Contacts.Company} detail.company The updated Company.
 */
/**
 * Fired if the current edited Company of the Editor has been deleted.
 * @event vDesk.Contacts.Company.Editor#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.Contacts.Company.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Contacts.Company} detail.company The deleted Company.
 */
/**
 * Initializes a new instance of the Editor class.
 * @class Represents an editor for viewing or editing the contents of an Company.
 * @param {vDesk.Contacts.Company} [Company = null] The Company to edit or view.
 * @param {Boolean} [Enabled = true] Flag indicating whether the Editor is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Contacts.Company} Company Gets or sets the Company to edit or view of the Editor.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @property {Boolean} Changed Gets a value indicating whether the data of the current Company of the Editor has been modified.
 * @memberOf vDesk.Contacts.Company
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 * @Todo Make GroupBox and EditControls, move buttons to editor window.
 */
vDesk.Contacts.Company.Editor = function Editor(Company, Enabled = true) {
    Ensure.Parameter(Company, vDesk.Contacts.Company, "Company");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the Company of the Editor has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    /**
     * The previous state of the Company of the Editor.
     * @type {vDesk.Contacts.Company}
     */
    let PreviousCompany = vDesk.Contacts.Company.FromDataView(Company);

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => GroupBox.Control
        },
        Company: {
            enumerable: true,
            get:        () => Company,
            set:        Value => {
                Ensure.Property(Value, vDesk.Contacts.Company, "Company");
                Company = Value;
                PreviousCompany = vDesk.Contacts.Company.FromDataView(Value);
                Name.Value = Value.Name;
                Street.Value = Value.Street;
                HouseNumber.Value = Value.HouseNumber;
                ZipCode.Value = Value.ZipCode;
                City.Value = Value.City;
                Country.Value = vDesk.Locale.Countries.find(Country => Country.ID === Value.Country)?.Name ?? "";
                PhoneNumber.Value = Value.PhoneNumber;
                FaxNumber.Value = Value.FaxNumber;
                Email.Value = Value.Email;
                Website.Value = Value.Website;
                Changed = false;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Name.Enabled = Value;
                Street.Enabled = Value;
                HouseNumber.Enabled = Value;
                ZipCode.Enabled = Value;
                City.Enabled = Value;
                Country.Enabled = Value;
                PhoneNumber.Enabled = Value;
                FaxNumber.Enabled = Value;
                Email.Enabled = Value;
                Website.Enabled = Value;
            }
        },
        Changed: {
            get: () => Changed
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#event:update
     * @fires vDesk.Contacts.Company.Editor#change
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {
            sender:  this,
            company: Company
        }).Dispatch(GroupBox.Control);
    };

    /**
     * Saves made changes.
     * @fires vDesk.Contacts.Company.Editor#create
     * @fires vDesk.Contacts.Company.Editor#update
     */
    this.Save = function() {

        const FoundCountry = vDesk.Locale.Countries.find(ExistingCountry => ExistingCountry.Name === Country.Value)?.Code ?? null;

        if(Company.ID !== null){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Contacts",
                        Command:    "UpdateCompany",
                        Parameters: {
                            ID:          Company.ID,
                            Name:        Name.Value,
                            Street:      Street.Value,
                            HouseNumber: HouseNumber.Value,
                            ZipCode:     ZipCode.Value,
                            City:        City.Value,
                            Country:     FoundCountry,
                            PhoneNumber: PhoneNumber.Value,
                            FaxNumber:   FaxNumber.Value,
                            Email:       Email.Value,
                            Website:     Website.Value
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        this.Company = vDesk.Contacts.Company.FromDataView(Response.Data);
                        new vDesk.Events.BubblingEvent("update", {
                            sender:  this,
                            company: Company
                        }).Dispatch(GroupBox.Control);
                    }
                }
            );
        }else{
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Contacts",
                        Command:    "CreateCompany",
                        Parameters: {
                            Name:        Name.Value,
                            Street:      Street.Value,
                            HouseNumber: HouseNumber.Value,
                            ZipCode:     ZipCode.Value,
                            City:        City.Value,
                            Country:     FoundCountry,
                            PhoneNumber: PhoneNumber.Value,
                            FaxNumber:   FaxNumber.Value,
                            Email:       Email.Value,
                            Website:     Website.Value
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        this.Company = vDesk.Contacts.Company.FromDataView(Response.Data);
                        new vDesk.Events.BubblingEvent("create", {
                            sender:  this,
                            company: Company
                        }).Dispatch(GroupBox.Control);
                    }
                }
            );
        }

    };

    /**
     * Deletes the current edited Company.
     * @fires vDesk.Contacts.Company.Editor#delete
     */
    this.Delete = function() {
        if(Company.ID !== null){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Contacts",
                        Command:    "DeleteCompany",
                        Parameters: {
                            ID: Company.ID
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        new vDesk.Events.BubblingEvent("delete", {
                            sender:  this,
                            company: Company
                        }).Dispatch(GroupBox.Control);
                        this.Company = new vDesk.Contacts.Company();
                    }
                }
            );
        }
    };

    /**
     * Resets the values of the current edited Company.
     */
    this.Reset = () => this.Company = PreviousCompany;

    /**
     * The name EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Name = new vDesk.Controls.EditControl(
        `${vDesk.Locale.vDesk.Name}*`,
        null,
        Type.String,
        Company.Name,
        null,
        true,
        Enabled
    );
    Name.Control.classList.add("Name");

    /**
     * The street EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Street = new vDesk.Controls.EditControl(
        `${vDesk.Locale.Contacts.Street}*`,
        null,
        Type.String,
        Company.Street,
        null,
        true,
        Enabled
    );
    Street.Control.classList.add("Street");

    /**
     * The house number EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const HouseNumber = new vDesk.Controls.EditControl(
        `${vDesk.Locale.Contacts.HouseNumber}*`,
        null,
        Type.String,
        Company.HouseNumber,
        null,
        true,
        Enabled
    );
    HouseNumber.Control.classList.add("HouseNumber");

    /**
     * The zip code EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const ZipCode = new vDesk.Controls.EditControl(
        `${vDesk.Locale.Contacts.ZipCode}*`,
        null,
        Type.Number,
        Company.ZipCode,
        null,
        true,
        Enabled
    );
    ZipCode.Control.classList.add("ZipCode");

    /**
     * The city EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const City = new vDesk.Controls.EditControl(
        `${vDesk.Locale.Contacts.City}*`,
        null,
        Type.String,
        Company.City,
        null,
        true,
        Enabled
    );
    City.Control.classList.add("City");

    /**
     * The country EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Country = new vDesk.Controls.EditControl(
        `${vDesk.Locale.Contacts.Country}*`,
        null,
        Extension.Type.Suggest,
        vDesk.Locale.Countries.find(Country => Country.Code === Company.Country.Code)?.Name ?? "",
        vDesk.Locale.Countries.map(Country => Country.Name),
        true,
        Enabled
    );
    Country.Control.classList.add("Country");

    /**
     * The phone number EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const PhoneNumber = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.PhoneNumber,
        null,
        Type.String,
        Company.PhoneNumber,
        null,
        false,
        Enabled
    );
    PhoneNumber.Control.classList.add("PhoneNumber");

    /**
     * The fax number EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const FaxNumber = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.FaxNumber,
        null,
        Type.String,
        Company.FaxNumber,
        null,
        false,
        Enabled
    );
    FaxNumber.Control.classList.add("FaxNumber");

    /**
     * The email EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Email = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.Email,
        null,
        Extension.Type.Email,
        Company.Email,
        null,
        false,
        Enabled
    );
    Email.Control.classList.add("Email");

    /**
     * The website EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Website = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.Website,
        null,
        Extension.Type.URL,
        Company.Website,
        null,
        false,
        Enabled
    );
    Website.Control.classList.add("Website");

    /**
     * The GroupBox of the Editor.
     * @type {vDesk.Controls.GroupBox}
     */
    const GroupBox = new vDesk.Controls.GroupBox(
        vDesk.Locale.Contacts.Company,
        [
            Name.Control,
            Street.Control,
            HouseNumber.Control,
            ZipCode.Control,
            City.Control,
            Country.Control,
            PhoneNumber.Control,
            FaxNumber.Control,
            Email.Control,
            Website.Control
        ]
    );
    GroupBox.Control.classList.add("CompanyEditor");
    GroupBox.Content.addEventListener("update", OnUpdate, false);
};