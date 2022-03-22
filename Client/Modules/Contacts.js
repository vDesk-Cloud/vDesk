"use strict";
/**
 * Initializes a new instance of the Contacts class.
 * @class Contacts Module
 * @property {HTMLDivElement} Control gets the underlying DOM-Node.
 * @property {String} Name Gets the name of the Module.
 * @property {String} Title Gets the title of the Module.
 * @property {String} Icon Gets the icon of the Module.
 * @memberOf Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 */
Modules.Contacts = function Contacts() {

    /**
     * Placeholder for non alphabetical letters.
     * @type {String}
     */
    const Symbol = "@!#";

    /**
     * The current selected Contact or Company.
     * @type {null|vDesk.Contacts.Contact|vDesk.Contacts.Company}
     */
    let Selected = null;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Name:    {
            enumerable: true,
            value:      "Contacts"
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Contacts.Module
        },
        Icon:    {
            enumerable: true,
            value:      vDesk.Visual.Icons.Contacts.Module
        }
    });

    /**
     * Creates a key for accessing the correct storageobject.
     * @param {String} Char The char to create a key of.
     * @return {String} The correct key to access a storageobject.
     */
    const Key = Char => /^[^a-zA-Z]/.test(Char) ? Symbol : (Char.charAt(0)).toUpperCase();

    /**
     * Adds a created Contact or Company to the Contacts module.
     * @param {vDesk.Contacts.Contact|vDesk.Contacts.Company} Model The created Contact or Company to add.
     */
    const Create = function(Model) {
        Ensure.Parameter(Model, [vDesk.Contacts.Contact, vDesk.Contacts.Company], "Model");
        if(Model instanceof vDesk.Contacts.Contact){
            //Add the Contact to the ContactCache.
            ContactCache.Add(Model);

            //Add the Contact to the Contacts Table if the first letter of its surname matches the letter of the current displayed contacts.
            if(SelectedItem.textContent === Key(Model.Surname)){
                ContactsTable.Rows.Add(Model);
            }
        }else if(Model instanceof vDesk.Contacts.Company){
            //Add the Company to the global Company collection.
            if(vDesk.Contacts.Companies.find(Company => Company.ID === Model.ID) === undefined){
                vDesk.Contacts.Companies.push(
                    {
                        ID:   Model.ID,
                        Name: Model.Name
                    }
                );
            }

            //Add the Company to the CompanyCache.
            CompanyCache.Add(Model);

            //Add the Company to the Companies Table if the first letter of its name matches the letter of the current displayed Companies.
            if(SelectedItem.textContent === Key(Model.Name)){
                CompaniesTable.Rows.Add(Model);
            }
        }
    };

    /**
     * Updates a Contact or Company of the Contacts module.
     * @param {vDesk.Contacts.Contact|vDesk.Contacts.Company} Model The Contact or Company to update.
     */
    const Update = function(Model) {
        Ensure.Parameter(Model, [vDesk.Contacts.Contact, vDesk.Contacts.Company], "Model");
        if(Model instanceof vDesk.Contacts.Contact){
            //Update the Contact of the ContactCache.
            ContactCache.Update(Model);

            //Remove the Contact from the Contacts Table.
            ContactsTable.Rows.RemoveWhere(Row => Row.ID === Model.ID);

            //Add the Contact to the Contacts Table if the first letter of its surname matches the letter of the current displayed Contacts.
            if(SelectedItem.textContent === Key(Model.Surname)){
                ContactsTable.Rows.Add(Model);
            }
        }else if(Model instanceof vDesk.Contacts.Company){
            //Update the Company of the global Company collection.
            const Company = vDesk.Contacts.Companies.find(Company => Company.ID === Model.ID);
            if(typeof Company !== undefined){
                Company.Name = Model.Name;
            }

            //Update the Company of the CompanyCache.
            CompanyCache.Update(Model);

            //Remove the Company from the Companies Table.
            CompaniesTable.Rows.RemoveWhere(Row => Row.ID === Model.ID);

            //Add the Company to the Companies Table if the first letter of its name matches the letter of the current displayed Companies.
            if(SelectedItem.textContent === Key(Model.Name)){
                CompaniesTable.Rows.Add(Model);
            }
        }
    };

    /**
     * Deletes a Contact or Company from the Contacts module.
     * @param {vDesk.Contacts.Contact|vDesk.Contacts.Company} Model The Contact or Company to delete.
     */
    const Delete = function(Model) {
        Ensure.Parameter(Model, [vDesk.Contacts.Contact, vDesk.Contacts.Company], "Model");
        if(Model instanceof vDesk.Contacts.Contact){
            //Remove the Contact from the ContactCache.
            ContactCache.Remove(Model);

            //Remove the Contact from the Contacts Table.
            ContactsTable.Rows.RemoveWhere(Row => Row.ID === Model.ID);
        }else if(Model instanceof vDesk.Contacts.Company){
            //Remove the Company to the global Company collection.
            const Company = vDesk.Contacts.Companies.find(Company => Company.ID === Model.ID);
            if(Company !== undefined){
                vDesk.Contacts.Companies.splice(vDesk.Contacts.Companies.indexOf(Company), 1);
            }

            //Remove the Company from the CompanyCache.
            CompanyCache.Remove(Model);

            //Remove the Company from the Companies Table.
            CompaniesTable.Rows.RemoveWhere(Row => Row.ID === Model.ID);
        }
    };

    /**
     * Deselects any selected contact or company.
     */
    const OnClick = () => {
        if(Selected !== null){
            Selected.Selected = false;
            Selected = null;
        }
        ViewToolBarItem.Enabled = false;
        EditToolBarItem.Enabled = false;
        DeleteToolBarItem.Enabled = false;
        ContextMenu.Hide();
    };

    /**
     * Fetches contacts or companies according to the clicked letter on the alphabetlist.
     * @param {MouseEvent} Event
     */
    const OnClickAlphabetList = Event => {
        Event.stopPropagation();
        SelectedItem.classList.remove("Selected");
        SelectedItem = Event.target;
        SelectedItem.classList.add("Selected");

        if(ContactsTabItem.Selected){
            ContactsTable.Rows.Clear();
            ContactCache.FetchContacts(
                SelectedItem.textContent,
                100,
                0,
                false,
                Contacts => ContactsTable.Rows = Contacts
            );
        }else if(CompaniesTabItem.Selected){
            CompaniesTable.Rows.Clear();
            CompanyCache.FetchCompanies(
                SelectedItem.textContent,
                100,
                0,
                false,
                Companies => CompaniesTable.Rows = Companies
            );
        }
        ContextMenu.Hide();
    };

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Contacts.Contact#event:select
     * @listens vDesk.Contacts.Company#event:select
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        if(Selected !== null){
            Selected.Selected = false;
        }
        Selected = Event.detail.sender;
        Selected.Selected = true;
        ViewToolBarItem.Enabled = true;
        EditToolBarItem.Enabled = Selected instanceof vDesk.Contacts.Company
            && vDesk.Security.User.Current.Permissions.UpdateCompany
            || Selected.AccessControlList.Write
            && vDesk.Security.User.Current.Permissions.UpdateContact;
        DeleteToolBarItem.Enabled = Selected instanceof vDesk.Contacts.Company
            && vDesk.Security.User.Current.Permissions.DeleteCompany
            || Selected.AccessControlList.Delete
            && vDesk.Security.User.Current.Permissions.DeleteContact;
        ContextMenu.Hide();
    };

    /**
     * Displays a Contact or Company.
     * @param {CustomEvent} Event
     */
    const OnOpen = Event => {
        new vDesk.Contacts.ViewerWindow(Event.detail.sender).Show();
        ContextMenu.Hide();
    };

    /**
     * Displays the ContextMenu.
     * @param {MouseEvent|CustomEvent} Event
     */
    const OnContext = Event => {
        if(ContextMenu.Visible){
            ContextMenu.Hide();
        }
        ContextMenu.Show(Event?.detail?.sender ?? Control, Event?.detail?.x ?? Event.pageX, Event?.detail?.y ?? Event.pageY);
    };

    /**
     * Handles the submit event and executes according actions.
     * @listens vDesk.Controls.ContextMenu#event:submit
     * @param {CustomEvent} Event
     */
    const OnSubmit = Event => {
        switch(Event.detail.action){
            case "open":
                new vDesk.Contacts.ViewerWindow(ContextMenu.Target).Show();
                break;
            case"createcontact":
                this.CreateContact();
                break;
            case "createcompany":
                this.CreateCompany();
                break;
            case "Edit":
                if(
                    ContextMenu.Target instanceof vDesk.Contacts.Contact
                    && ContextMenu.Target.AccessControlList.Write
                    && vDesk.Security.User.Current.Permissions.UpdateContact
                ){
                    this.UpdateContact(ContextMenu.Target);
                }else if(
                    ContextMenu.Target instanceof vDesk.Contacts.Company
                    && vDesk.Security.User.Current.Permissions.UpdateCompany
                ){
                    this.UpdateCompany(ContextMenu.Target);
                }
                break;
            case "delete":
                if(
                    ContextMenu.Target instanceof vDesk.Contacts.Contact
                    && ContextMenu.Target.AccessControlList.Delete
                    && vDesk.Security.User.Current.Permissions.DeleteContact
                ){
                    this.DeleteContact(ContextMenu.Target);
                }else if(ContextMenu.Target instanceof vDesk.Contacts.Company){
                    this.DeleteCompany(ContextMenu.Target);
                }
                break;
        }
        ContextMenu.Hide();
    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @listens vDesk.Contacts.Contact.Editor#event:create
     * @param {CustomEvent} Event
     */
    const OnCreateContact = Event => Create(Event.detail.contact);

    /**
     * Eventhandler that listens on the global 'vDesk.Contacts.Contact.Created' event.
     * @param {MessageEvent} Event
     */
    const OnContactsContactCreated = Event => {
        if(ContactCache.Find(Number.parseInt(Event.data)) === null){
            ContactCache.FetchContact(Number.parseInt(Event.data), Create);
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Contacts.Contact.Created", OnContactsContactCreated, false);

    /**
     * Creates a new Contact.
     */
    this.CreateContact = () => this.UpdateContact(new vDesk.Contacts.Contact());

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Contacts.Contact.Editor#event:update
     * @param {CustomEvent} Event
     */
    const OnUpdateContact = Event => Update(Event.detail.contact);

    /**
     * Eventhandler that listens on the vDesk.Contacts.Contact.Updated event.
     * @param {MessageEvent} Event
     */
    const OnContactsContactUpdated = Event => {
        if(ContactCache.Find(Number.parseInt(Event.data)) !== null){
            ContactCache.FetchContact(Number.parseInt(Event.data), Update)
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Contacts.Contact.Updated", OnContactsContactUpdated, false);

    /**
     * Updates a Contact.
     * @param {vDesk.Contacts.Contact} Contact The Contact to update.
     */
    this.UpdateContact = function(Contact) {
        Ensure.Parameter(Contact, vDesk.Contacts.Contact, "Contact");
        const Window = new vDesk.Contacts.Contact.Editor.Window(Contact);
        Window.Control.addEventListener("create", OnCreateContact);
        Window.Control.addEventListener("update", OnUpdateContact);
        Window.Control.addEventListener("delete", OnDeleteContact);
        Window.Show();
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.Contacts.Contact.Editor#event:delete
     * @param {CustomEvent} Event
     */
    const OnDeleteContact = Event => Delete(Event.detail.contact);

    /**
     * Eventhandler that listens on the global 'vDesk.Contacts.Contact.Deleted' event.
     * @param {MessageEvent} Event
     */
    const OnContactsContactDeleted = Event => {
        const Contact = ContactCache.Find(Number.parseInt(Event.data));
        if(Contact !== null){
            Delete(Contact);
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Contacts.Contact.Deleted", OnContactsContactDeleted, false);

    /**
     * Deletes a Contact.
     * @param {vDesk.Contacts.Contact} Contact The Contact to delete.
     */
    this.DeleteContact = function(Contact) {
        Ensure.Parameter(Contact, vDesk.Contacts.Contact, "Contact");
        const Editor = new vDesk.Contacts.Contact.Editor(Contact);
        Editor.Control.addEventListener("delete", OnDeleteContact, false);
        Editor.Delete();
    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @listens vDesk.Contacts.Company.Editor#event:create
     * @param {CustomEvent} Event
     */
    const OnCreateCompany = Event => Create(Event.detail.company);

    /**
     * Eventhandler that listens on the global 'vDesk.Contacts.Company.Created' event.
     * @param {MessageEvent} Event
     */
    const OnContactsCompanyCreated = Event => {
        if(CompanyCache.Find(Number.parseInt(Event.data)) === null){
            CompanyCache.FetchCompany(Number.parseInt(Event.data), Create);
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Contacts.Company.Created", OnContactsCompanyCreated, false);

    /**
     * Creates a new Company.
     */
    this.CreateCompany = () => this.UpdateCompany(new vDesk.Contacts.Company());

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Contacts.Company.Editor#event:update
     * @param {CustomEvent} Event
     */
    const OnUpdateCompany = Event => Update(Event.detail.company);

    /**
     * Eventhandler that listens on the global 'vDesk.Contacts.Company.Updated' event.
     * @param {MessageEvent} Event
     */
    const OnContactsCompanyUpdated = Event => {
        if(CompanyCache.Find(Number.parseInt(Event.data)) !== null){
            CompanyCache.FetchCompany(Number.parseInt(Event.data), Update)
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Contacts.Company.Updated", OnContactsCompanyUpdated, false);

    /**
     * Updates a Company.
     * @param {vDesk.Contacts.Company} Company The Company to update.
     */
    this.UpdateCompany = function(Company) {
        Ensure.Parameter(Company, vDesk.Contacts.Company, "Company");
        const Window = new vDesk.Contacts.Company.Editor.Window(Company);
        Window.Control.addEventListener("create", OnCreateCompany, false);
        Window.Control.addEventListener("update", OnUpdateCompany, false);
        Window.Control.addEventListener("delete", OnDeleteCompany, false);
        Window.Show();
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.Contacts.Company.Editor#event:delete
     * @param {CustomEvent} Event
     */
    const OnDeleteCompany = Event => Delete(Event.detail.company);

    /**
     * Eventhandler that listens on the global 'vDesk.Contacts.Company.Deleted' event.
     * @param {MessageEvent} Event
     */
    const OnContactsCompanyDeleted = Event => {
        const Company = CompanyCache.Find(Number.parseInt(Event.data));
        if(Company !== null){
            Delete(Company);
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Contacts.Company.Deleted", OnContactsCompanyDeleted, false);

    /**
     * Deletes a Company.
     * @param {vDesk.Contacts.Company} Company The Company to delete.
     */
    this.DeleteCompany = function(Company) {
        Ensure.Parameter(Company, vDesk.Contacts.Company, "Company");
        const Editor = new vDesk.Contacts.Company.Editor(Company);
        Editor.Control.addEventListener("delete", OnDeleteCompany, false);
        Editor.Delete();
    };

    /**
     * Loads the Contacts Module.
     */
    this.Load = function() {
        vDesk.Header.ToolBar.Groups = [NewToolBarGroup, SelectionToolBarGroup];
        Control.addEventListener("click", OnClick);
        Control.addEventListener("select", OnSelect);
        Control.addEventListener("open", OnOpen);
        Control.addEventListener("context", OnContext);
        Control.addEventListener("contextmenu", OnContext);

    };

    /**
     * Unloads the Contacts Module.
     */
    this.Unload = function() {
        Control.removeEventListener("click", OnClick);
        Control.removeEventListener("select", OnSelect);
        Control.removeEventListener("open", OnOpen);
        Control.removeEventListener("context", OnContext);
        Control.removeEventListener("contextmenu", OnContext);
    };

    /**
     * The ContextMenu of the Contacts module.
     * @type {vDesk.Controls.ContextMenu}
     */
    const ContextMenu = new vDesk.Controls.ContextMenu(
        [
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale.vDesk.Open,
                "open",
                vDesk.Visual.Icons.View,
                Model => Model instanceof vDesk.Contacts.Contact || Model instanceof vDesk.Contacts.Company
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale.vDesk.Edit,
                "Edit",
                vDesk.Visual.Icons.Edit,
                Model => Model instanceof vDesk.Contacts.Contact
                    && Model.AccessControlList.Write
                    && vDesk.Security.User.Current.Permissions.UpdateContact
                    || Model instanceof vDesk.Contacts.Company
                    && vDesk.Security.User.Current.Permissions.UpdateCompany
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale.vDesk.Delete,
                "delete",
                vDesk.Visual.Icons.Delete,
                Model => Model instanceof vDesk.Contacts.Contact
                    && Model.AccessControlList.Delete
                    && vDesk.Security.User.Current.Permissions.DeleteContact
                    || Model instanceof vDesk.Contacts.Company
                    && vDesk.Security.User.Current.Permissions.DeleteCompany
            ),
            new vDesk.Controls.ContextMenu.Group(
                vDesk.Locale.vDesk.New,
                vDesk.Visual.Icons.TriangleRight,
                () => true,
                [
                    new vDesk.Controls.ContextMenu.Item(vDesk.Locale.Contacts.Contact, "createcontact", vDesk.Visual.Icons.Security.CreateUser),
                    new vDesk.Controls.ContextMenu.Item(vDesk.Locale.Contacts.CompanyContact, "createcompany", vDesk.Visual.Icons.Contacts.CreateCompany)
                ]
            )
        ]
    );
    ContextMenu.Control.addEventListener("submit", OnSubmit);

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Contacts Font";

    /**
     * The alphabet list of the Contacts module.
     * @type {HTMLUListElement}
     */
    const AlphabetList = document.createElement("ul");
    AlphabetList.className = "Alphabet";
    AlphabetList.addEventListener("click", OnClickAlphabetList, false);

    //Create alphabetical items.
    for(let i = 65; i < 91; i++){
        const Item = document.createElement("li");
        Item.className = "Char Font Dark BorderLight";
        Item.textContent = String.fromCharCode(i);
        AlphabetList.appendChild(Item);
    }

    //Create item for non alphabetical characters.
    const Item = document.createElement("li");
    Item.className = "Char Font Dark BorderLight";
    Item.textContent = "@!#";
    AlphabetList.appendChild(Item);
    Control.appendChild(AlphabetList);

    /**
     * The current selected char-item of the Contacts module.
     * @type {HTMLLIElement}
     */
    let SelectedItem = AlphabetList.children[0];
    SelectedItem.classList.add("Selected");

    /**
     * The contacts Table of the Contacts module.
     * @type vDesk.Controls.Table
     */
    const ContactsTable = new vDesk.Controls.Table(
        [
            {
                Name:  "Gender",
                Type:  Type.Number,
                Label: vDesk.Locale.Contacts.Gender
            },
            {
                Name:  "Title",
                Type:  Type.String,
                Label: vDesk.Locale.vDesk.MaximizeTitle
            },
            {
                Name:  "Surname",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.Surname
            },
            {
                Name:  "Forename",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.Forename
            },
            {
                Name:  "Street",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.Street
            },
            {
                Name:  "HouseNumber",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.HouseNumber
            },
            {
                Name:  "ZipCode",
                Type:  Type.Number,
                Label: vDesk.Locale.Contacts.ZipCode
            },
            {
                Name:  "City",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.City
            }
        ]
    );

    /**
     * The ContactCache of the Contacts module.
     * @type {vDesk.Contacts.Contact.Cache}
     */
    const ContactCache = new vDesk.Contacts.Contact.Cache();
    ContactCache.FetchContacts(
        SelectedItem.textContent,
        100,
        0,
        false,
        Contacts => ContactsTable.Rows = Contacts
    );

    /**
     * The contacts TabItem of the Contacts module.
     * @type vDesk.Controls.TabControl.TabItem
     */
    const ContactsTabItem = new vDesk.Controls.TabControl.TabItem(vDesk.Locale.Contacts.Module, ContactsTable.Control);

    /**
     * The Company Table of the Contacts module.
     * @type vDesk.Controls.Table
     */
    const CompaniesTable = new vDesk.Controls.Table(
        [
            {
                Name:  "Name",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.Name
            },
            {
                Name:  "Street",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.Street
            },
            {
                Name:  "HouseNumber",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.HouseNumber
            },
            {
                Name:  "ZipCode",
                Type:  Type.Number,
                Label: vDesk.Locale.Contacts.ZipCode
            },
            {
                Name:  "City",
                Type:  Type.String,
                Label: vDesk.Locale.Contacts.City
            }
        ]
    );

    /**
     * The CompanyCache of the Contacts module.
     * @type {vDesk.Contacts.Company.Cache}
     */
    const CompanyCache = new vDesk.Contacts.Company.Cache();
    CompanyCache.FetchCompanies(
        SelectedItem.textContent,
        100,
        0,
        false,
        Companies => CompaniesTable.Rows = Companies
    );

    /**
     * The Company TabItem of the Contacts module.
     * @type vDesk.Controls.TabControl.TabItem
     */
    const CompaniesTabItem = new vDesk.Controls.TabControl.TabItem(vDesk.Locale.Contacts.Companies, CompaniesTable.Control);

    /**
     * The TabControl of the Contacts module.
     * @type vDesk.Controls.TabControl
     */
    const TabControl = new vDesk.Controls.TabControl([ContactsTabItem, CompaniesTabItem]);
    Control.appendChild(TabControl.Control);

    /**
     * The new contact ToolBar Item of the Contacts module.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NewContactToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Contacts.Contact,
        vDesk.Visual.Icons.Security.CreateUser,
        vDesk.Security.User.Current.Permissions.CreateContact,
        this.CreateContact
    );

    /**
     * The new company ToolBar Item of the Contacts module.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NewCompanyToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Contacts.CompanyContact,
        vDesk.Visual.Icons.Contacts.CreateCompany,
        vDesk.Security.User.Current.Permissions.CreateCompany,
        this.CreateCompany
    );

    /**
     * The new ToolBar Group of the Contacts module.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const NewToolBarGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale.vDesk.New,
        [
            NewContactToolBarItem,
            NewCompanyToolBarItem
        ]
    );

    /**
     * The view ToolBar Item of the Contacts module.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ViewToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Open,
        vDesk.Visual.Icons.View,
        false,
        () => {
            if(Selected !== null){
                new vDesk.Contacts.ViewerWindow(Selected).Show();
            }
        }
    );

    /**
     * The edit ToolBar Item of the Contacts module.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const EditToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Edit,
        vDesk.Visual.Icons.Edit,
        false,
        () => {
            if(
                Selected instanceof vDesk.Contacts.Contact
                && Selected.AccessControlList.Write
                && vDesk.Security.User.Current.Permissions.UpdateContact
            ){
                this.UpdateContact(Selected);
            }else if(
                Selected instanceof vDesk.Contacts.Company
                && vDesk.Security.User.Current.Permissions.UpdateCompany
            ){
                this.UpdateCompany(Selected);
            }
        }
    );

    /**
     * The delete ToolBar Item of the Contacts module.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const DeleteToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Delete,
        vDesk.Visual.Icons.Delete,
        false,
        () => {
            if(
                Selected instanceof vDesk.Contacts.Contact
                && Selected.AccessControlList.Delete
                && vDesk.Security.User.Current.Permissions.DeleteContact
            ){
                this.DeleteContact(Selected);
            }else if(
                Selected instanceof vDesk.Contacts.Company
                && vDesk.Security.User.Current.Permissions.DeleteCompany

            ){
                this.DeleteCompany(Selected);
            }
        }
    );

    /**
     * The selection ToolBar Group of the Contacts module.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const SelectionToolBarGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale.vDesk.MaximizeSelection,
        [
            ViewToolBarItem,
            EditToolBarItem,
            DeleteToolBarItem
        ]
    );

};

Modules.Contacts.Implements(vDesk.Modules.IVisualModule);