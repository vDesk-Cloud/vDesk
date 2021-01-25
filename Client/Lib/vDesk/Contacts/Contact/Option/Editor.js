"use strict";
/**
 * Fired if the Options of the Contact of the Editor have been changed.
 * @event vDesk.Contacts.Contact.Option.Editor#changed
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'changed' event.
 * @property {vDesk.Contacts.Contact.Option.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Contacts.Contact} detail.contact The changed Contact.
 */
/**
 * Initializes a new instance of the Editor class.
 * @class Represents an editor for viewing or editing the Options of a Contact.
 * @param {?vDesk.Contacts.Contact} Contact Initializes the Editor with the specified Contact.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Editor is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Contacts.Contact} Contact Gets or sets the Contact to edit or view of the Editor.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @property {Boolean} Changed Gets a value indicating whether the data of the current Contact of the Editor has been modified.
 * @memberOf vDesk.Contacts.Contact.Option
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Contacts.Contact.Option.Editor = function Editor(Contact, Enabled = true) {
    Ensure.Parameter(Contact, vDesk.Contacts.Contact, "Contact");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The previous state of the current edited Options of the Editor.
     * @type {vDesk.Contacts.Contact}
     */
    let PreviousContact = vDesk.Contacts.Contact.FromDataView(Contact);

    /**
     * The added Options of the Contact of the Editor.
     * @type Array<vDesk.Contacts.Contact.Option>
     */
    let Added = [];

    /**
     * The updated Options of the Contact of the Editor.
     * @type Array<vDesk.Contacts.Contact.Option>
     */
    let Updated = [];

    /**
     * The deleted Options of the Contact of the Editor.
     * @type Array<vDesk.Contacts.Contact.Option>
     */
    let Deleted = [];

    /**
     * Flag indicating whether the Options have been changed.
     * @type {Boolean}
     */
    let Changed = false;

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
                //Remove Options.
                Contact.Options.forEach(Option => Options.removeChild(Option.Control));
                Added = [];
                Updated = [];
                Deleted = [];

                Contact = Value;
                PreviousContact = vDesk.Contacts.Contact.FromDataView(Value);

                //Append new Options.
                const Fragment = document.createDocumentFragment();
                Value.Options.forEach(Option => Fragment.appendChild(Option.Control));
                Options.appendChild(Fragment);

                Changed = false;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Contact.Options.forEach(Option => Option.Enabled = Value);
                Types.disabled = !Value;
                Add.disabled = !Value;
            }
        },
        Changed: {
            enumerable: true,
            get:        () => Changed
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Contacts.Contact.Option.Editor#changed
     */
    const OnClick = () => {
        const Option = new vDesk.Contacts.Contact.Option(null, Types.selectedIndex);
        Contact.Options.push(Option);
        Added.push(Option);
        Options.appendChild(Option.Control);
        Changed = true;
        if(Contact.ID !== null) {
            new vDesk.Events.BubblingEvent("change", {
                sender:  this,
                contact: Contact
            }).Dispatch(GroupBox.Control);
        }
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @param {CustomEvent} Event
     * @fires vDesk.Contacts.Contact.Option.Editor#changed
     * @listens vDesk.Contacts.Contact.Option#event:update
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Updated.push(Event.detail.sender);
        Changed = true;
        if(Contact.ID !== null) {
            new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(GroupBox.Control);
        }
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @fires vDesk.Contacts.Contact.Option.Editor#changed
     * @listens vDesk.Contacts.Contact.Option#event:delete
     */
    const OnDelete = Event => {
        Event.stopPropagation();
        Options.removeChild(Event.detail.sender.Control);
        Contact.Options.splice(Contact.Options.indexOf(Event.detail.sender), 1);

        //Remove the option if it has been marked as 'added'.
        if(Event.detail.sender.ID === null) {
            Added.splice(Added.indexOf(Event.detail.sender), 1);
        } else {
            //Check if the option has been updated before
            const Index = Updated.indexOf(Event.detail.sender);
            if(~Index) {
                Updated.splice(Index, 1);
            }
            //Mark the option as 'deleted'.
            Deleted.push(Event.detail.sender);
        }
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(GroupBox.Control);
    };

    /**
     * Saves possible changes.
     * @return {Boolean} True if the made changes have been successfully saved; otherwise, false.
     */
    this.Save = function() {
        if(Contact.ID !== null) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Contacts",
                        Command:    "SetContactOptions",
                        Parameters: {
                            ID:     Contact.ID,
                            Add:    Added.map(Option => ({
                                Type:  Option.Type,
                                Value: Option.Value
                            })),
                            Update: Updated.map(Option => ({
                                ID:    Option.ID,
                                Type:  Option.Type,
                                Value: Option.Value
                            })),
                            Delete: Deleted.map(Option => Option.ID)
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        Contact.Options = Response.Data.map(Option => vDesk.Contacts.Contact.Option.FromDataView(Option));
                        this.Contact = Contact;
                    } else {
                        alert(Response.Data);
                    }
                }
            );
        }
    };

    /**
     * Resets the Options of the Contact of the Editor.
     */
    this.Reset = () => this.Contact = PreviousContact;

    /**
     * The type select of the Editor.
     * @type {HTMLSelectElement}
     */
    const Types = document.createElement("select");
    Types.className = "Type TextBox";
    Types.disabled = !Enabled;

    /**
     * The telephone option of the Editor.
     * @type {HTMLOptionElement}
     */
    const Telephone = document.createElement("option");
    Telephone.textContent = vDesk.Locale.Contacts.PhoneNumber;
    Telephone.value = vDesk.Contacts.Contact.Option.Telephone;
    Telephone.selected = true;
    Types.options.add(Telephone);

    /**
     * The fax option of the Editor.
     * @type {HTMLOptionElement}
     */
    const Fax = document.createElement("option");
    Fax.textContent = vDesk.Locale.Contacts.FaxNumber;
    Fax.value = vDesk.Contacts.Contact.Option.Fax;
    Types.options.add(Fax);

    /**
     * The email option of the Editor.
     * @type {HTMLOptionElement}
     */
    const Email = document.createElement("option");
    Email.textContent = vDesk.Locale.Contacts.Email;
    Email.value = vDesk.Contacts.Contact.Option.Email;
    Types.options.add(Email);

    /**
     * The website option of the Editor.
     * @type {HTMLOptionElement}
     */
    const Website = document.createElement("option");
    Website.textContent = vDesk.Locale.Contacts.Website;
    Website.value = vDesk.Contacts.Contact.Option.Website;
    Types.options.add(Website);

    /**
     * The add button of the Editor.
     * @type {HTMLButtonElement}
     */
    const Add = document.createElement("button");
    Add.className = "Button Add BorderDark";
    Add.textContent = "+";
    Add.title = vDesk.Locale.Contacts.CreateContactOption;
    Add.disabled = !Enabled;
    Add.addEventListener("click", OnClick, false);

    /**
     * The options list of the Editor.
     * @type {HTMLUListElement}
     */
    const Options = document.createElement("ul");
    Options.className = "Options";
    Options.addEventListener("update", OnUpdate, false);
    Options.addEventListener("delete", OnDelete, false);
    Contact.Options.forEach(Option => Options.appendChild(Option.Control));

    /**
     * The GroupBox of the Editor.
     * @type {vDesk.Controls.GroupBox}
     */
    const GroupBox = new vDesk.Controls.GroupBox(
        vDesk.Locale.Contacts.ContactOptions,
        [
            Types,
            Add,
            Options
        ]
    );
    GroupBox.Control.classList.add("ContactOptionEditor");

};