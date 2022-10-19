"use strict";
/**
 * Fired if the data of the Contact of the Editor has been modified.
 * @event vDesk.Contacts.Contact.Editor#change
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'change' event.
 * @property {vDesk.Contacts.Contact.Editor} detail.sender The current instance of the Editor.
 */
/**
 * Fired if a new Contact has been created.
 * @event vDesk.Contacts.Contact.Editor#create
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'create' event.
 * @property {vDesk.Contacts.Contact.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Contacts.Contact} detail.contact The newly created Contact.
 */
/**
 * Fired if the current edited Contact of the Editor has been updated.
 * @event vDesk.Contacts.Contact.Editor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Contacts.Contact.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Contacts.Contact} detail.contact The updated Contact.
 */
/**
 * Fired if the current edited Contact of the Editor has been deleted.
 * @event vDesk.Contacts.Contact.Editor#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.Contacts.Contact.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Contacts.Contact} detail.contact The deleted Contact.
 */
/**
 * Initializes a new instance of the Editor class.
 * @class Represents an editor for viewing or editing the contents of a Contact.
 * @param {?vDesk.Contacts.Contact} Contact Initializes the Editor with the specified Contact.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Editor is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Contacts.Contact} Contact Gets or sets the contact to edit or view of the Editor.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @property {Boolean} Changed Gets a value indicating whether the data of the current contact of the Editor has been modified.
 * @fires changed Fired if the data of the contact of the Editor has been modified.
 * @memberOf vDesk.Contacts.Contact
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 */
vDesk.Contacts.Contact.Editor = function Editor(Contact, Enabled = true) {
    Ensure.Parameter(Contact, vDesk.Contacts.Contact, "Contact");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the contact of the Editor has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    /**
     * The previous state of the current edited Contact of the Editor.
     * @type {vDesk.Contacts.Contact}
     */
    let Previous = vDesk.Contacts.Contact.FromDataView(Contact);

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => GroupBox.Control
        },
        Contact: {
            enumerable: true,
            get:        () => Contact,
            set:        Value => {
                Ensure.Property(Value, vDesk.Contacts.Contact, "Contact");
                Contact = Value;
                Previous = vDesk.Contacts.Contact.FromDataView(Value);
                Owner.textContent = `${vDesk.Locale.Security.Owner}: ${Value.Owner.Name}`;
                Gender.Value = Value.Gender.toString();
                Title.Value = Value.Title;
                Forename.Value = Value.Forename;
                Surname.Value = Value.Surname;
                Street.Value = Value.Street;
                HouseNumber.Value = Value.HouseNumber;
                ZipCode.Value = Value.ZipCode;
                City.Value = Value.City;
                Country.Value = vDesk.Locale.Countries.find(Country => Country.Code === Value.Country.Code)?.Name ?? null;
                Company.Value = vDesk.Contacts.Companies.find(Company => Company.ID === Value.Company.ID)?.Name ?? null;
                Annotations.Value = Value.Annotations;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Gender.Enabled = Value;
                Title.Enabled = Value;
                Forename.Enabled = Value;
                Surname.Enabled = Value;
                Street.Enabled = Value;
                HouseNumber.Enabled = Value;
                ZipCode.Enabled = Value;
                City.Enabled = Value;
                Country.Enabled = Value;
                Company.Enabled = Value;
                Annotations.Enabled = Value;
            }
        },
        Changed: {
            get: () => Changed || ContactOptionEditor.Changed
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#event:update
     * @fires vDesk.Contacts.Contact.Editor#change
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {
            sender:  this,
            contact: Contact
        }).Dispatch(GroupBox.Control);
    };

    /**
     * Saves possible changes.
     * @return {Boolean} True if the made changes have been successfully saved; otherwise, false.
     */
    this.Save = function() {
        if(Contact.ID === null){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Contacts",
                        Command:    "CreateContact",
                        Parameters: {
                            Gender:      Number.parseInt(Gender.Value),
                            Title:       Title.Value,
                            Forename:    Forename.Value,
                            Surname:     Surname.Value,
                            Street:      Street.Value,
                            HouseNumber: HouseNumber.Value,
                            ZipCode:     ZipCode.Value,
                            City:        City.Value,
                            Country:     vDesk.Locale.Countries.find(ExistingCountry => ExistingCountry.Name === Country.Value)?.Code ?? null,
                            Company:     vDesk.Contacts.Companies.find(ExistingCompany => ExistingCompany.Name === Company.Value)?.ID ?? null,
                            Annotations: Annotations.Value
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        if(ContactOptionEditor.Changed){
                            Contact.ID = Response.Data.ID;
                            ContactOptionEditor.Save();
                        }
                        this.Contact = vDesk.Contacts.Contact.FromDataView(Response.Data);
                        new vDesk.Events.BubblingEvent("create", {
                            sender:  this,
                            contact: Contact
                        }).Dispatch(GroupBox.Control);
                    }
                }
            );
        }else{
            if(ContactOptionEditor.Changed){
                ContactOptionEditor.Save();
            }
            if(Changed){
                vDesk.Connection.Send(
                    new vDesk.Modules.Command(
                        {
                            Module:     "Contacts",
                            Command:    "UpdateContact",
                            Parameters: {
                                ID:          Contact.ID,
                                Gender:      Number.parseInt(Gender.Value),
                                Title:       Title.Value,
                                Forename:    Forename.Value,
                                Surname:     Surname.Value,
                                Street:      Street.Value,
                                HouseNumber: HouseNumber.Value,
                                ZipCode:     ZipCode.Value,
                                City:        City.Value,
                                Country:     vDesk.Locale.Countries.find(ExistingCountry => ExistingCountry.Name === Country.Value)?.Code ?? null,
                                Company:     vDesk.Contacts.Companies.find(ExistingCompany => ExistingCompany.Name === Company.Value)?.ID ?? null,
                                Annotations: Annotations.Value
                            },
                            Ticket:     vDesk.Security.User.Current.Ticket
                        }
                    ),
                    Response => {
                        if(Response.Status){
                            this.Contact = vDesk.Contacts.Contact.FromDataView(Response.Data);
                            new vDesk.Events.BubblingEvent("update", {
                                sender:  this,
                                contact: Contact
                            }).Dispatch(GroupBox.Control);
                        }
                    }
                );
            }
        }
    };

    /**
     * Deletes the current edited Contact.
     * @fires vDesk.Contacts.Contact.Editor#delete
     */
    this.Delete = function() {
        if(Contact.ID !== null){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Contacts",
                        Command:    "DeleteContact",
                        Parameters: {
                            ID: Contact.ID
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        new vDesk.Events.BubblingEvent("delete", {
                            sender:  this,
                            contact: Contact
                        }).Dispatch(GroupBox.Control);
                        this.Contact = new vDesk.Contacts.Contact();
                    }
                }
            );
        }
    };

    /**
     * Resets the current edited Contact to its original state.
     */
    this.Reset = () => this.Contact = Previous;

    /**
     * The owner row of the Editor.
     * @type {HTMLLIElement}
     */
    const Owner = document.createElement("li");
    Owner.className = "Owner Font Dark BorderLight";
    Owner.textContent = `${vDesk.Locale.Security.Owner}: ${Contact.Owner.Name}`;

    /**
     * The enumeration of genders to use.
     * @type {Object}
     */
    const Genders = {};
    Genders[vDesk.Locale.Contacts.GenderMale] = "0";
    Genders[vDesk.Locale.Contacts.GenderFemale] = "1";

    /**
     * The gender EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Gender = new vDesk.Controls.EditControl(
        `${vDesk.Locale.Contacts.Gender}*`,
        null,
        Extension.Type.Enum,
        Contact.Gender.toString(),
        Genders,
        true,
        Enabled
    );
    Gender.Control.classList.add("Gender");

    /**
     * The title EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Title = new vDesk.Controls.EditControl(
        vDesk.Locale.vDesk.Title,
        null,
        Type.String,
        Contact.Title,
        null,
        false,
        Enabled
    );
    Title.Control.classList.add("Title");

    /**
     * The forename EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Forename = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.Forename,
        null,
        Type.String,
        Contact.Forename,
        null,
        false,
        Enabled
    );
    Forename.Control.classList.add("Forename");

    /**
     * The surname EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Surname = new vDesk.Controls.EditControl(
        `${vDesk.Locale.Contacts.Surname}*`,
        null,
        Type.String,
        Contact.Surname,
        null,
        true,
        Enabled
    );
    Surname.Control.classList.add("Surname", "BorderLight");

    /**
     * The surname EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Street = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.Street,
        null,
        Type.String,
        Contact.Street,
        null,
        false,
        Enabled
    );
    Street.Control.classList.add("Street");

    /**
     * The house number EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const HouseNumber = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.HouseNumber,
        null,
        Type.String,
        Contact.HouseNumber,
        null,
        false,
        Enabled
    );
    HouseNumber.Control.classList.add("HouseNumber");

    /**
     * The zip code number EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const ZipCode = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.ZipCode,
        null,
        Type.Number,
        Contact.ZipCode,
        null,
        false,
        Enabled
    );
    ZipCode.Control.classList.add("ZipCode");

    /**
     * The city EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const City = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.City,
        null,
        Type.String,
        Contact.City,
        null,
        false,
        Enabled
    );
    City.Control.classList.add("City");

    /**
     * The country EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Country = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.Country,
        null,
        Extension.Type.Suggest,
        vDesk.Locale.Countries.find(Country => Country.Code === Contact.Country.Code)?.Name ?? null,
        vDesk.Locale.Countries.map(Country => Country.Name),
        false,
        Enabled
    );
    Country.Control.classList.add("Country");

    /**
     * The ContactOptionEditor of the Editor.
     * @type {vDesk.Contacts.Contact.Option.Editor}
     */
    const ContactOptionEditor = new vDesk.Contacts.Contact.Option.Editor(Contact, Enabled);

    /**
     * The Company EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Company = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.Company,
        null,
        Extension.Type.Suggest,
        vDesk.Contacts.Companies.find(Company => Company.ID === Contact.Company.ID)?.Name ?? null,
        vDesk.Contacts.Companies.map(Company => Company.Name),
        false,
        Enabled
    );
    Company.Control.classList.add("Company");

    /**
     * The city EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Annotations = new vDesk.Controls.EditControl(
        vDesk.Locale.Contacts.Annotations,
        null,
        Extension.Type.Text,
        Contact.Annotations,
        null,
        false,
        Enabled
    );
    Annotations.Control.classList.add("Annotations");

    /**
     * The GroupBox of the Editor.
     * @type {vDesk.Controls.GroupBox}
     */
    const GroupBox = new vDesk.Controls.GroupBox(
        vDesk.Locale.Contacts.Contact,
        [
            Owner,
            Gender.Control,
            Title.Control,
            Forename.Control,
            Surname.Control,
            Street.Control,
            HouseNumber.Control,
            ZipCode.Control,
            City.Control,
            Country.Control,
            ContactOptionEditor.Control,
            Company.Control,
            Annotations.Control
        ]
    );
    GroupBox.Control.classList.add("ContactEditor");
    GroupBox.Content.addEventListener("update", OnUpdate, false);
};