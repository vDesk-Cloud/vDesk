"use strict";
/**
 * Initializes a new instance of the Viewer class.
 * @class Represents a viewer-control for displaying the data of a contact.
 * @param {vDesk.Contacts.Contact} Contact Initializes the Viewer with the specified Contact.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @memberOf vDesk.Contacts
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Contacts.Contact.Viewer = function Viewer(Contact) {
    Ensure.Parameter(Contact, vDesk.Contacts.Contact, "Contact");

    Object.defineProperty(this, "Control", {
        enumerable: true,
        get:        () => Control
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "ContactViewer Font";

    /**
     * The name row of the Viewer.
     * @type {HTMLHeadingElement}
     */
    const NameRow = document.createElement("h2");
    NameRow.className = "Row Name Font Dark";
    NameRow.textContent = `${Contact.Gender === 0 ? vDesk.Locale.Contacts.GenderMale : vDesk.Locale.Contacts.GenderFemale} ${Contact.Title} ${Contact.Forename} ${Contact.Surname}`;

    /**
     * The address GroupBox of the Viewer.
     * @type {vDesk.Controls.GroupBox}
     */
    const AddressGroupBox = new vDesk.Controls.GroupBox(vDesk.Locale.Contacts.Address);

    /**
     * The address list of the Viewer.
     * @type {HTMLUListElement}
     */
    const AddressList = document.createElement("ul");
    AddressList.className = "Address";

    /**
     * The street row of the Viewer.
     * @type {HTMLLIElement}
     */
    const StreetRow = document.createElement("li");
    StreetRow.className = "Row Street";
    StreetRow.textContent = `${Contact.Street} ${Contact.HouseNumber}`;

    /**
     * The city row of the Viewer.
     * @type {HTMLLIElement}
     */
    const CityRow = document.createElement("li");
    CityRow.className = "Row City";
    CityRow.textContent = `${Contact.ZipCode > 0 ? Contact.ZipCode : ""} ${Contact.City}`;

    /**
     * The country row of the Viewer.
     * @type {HTMLLIElement}
     */
    const CountryRow = document.createElement("li");
    CountryRow.className = "Row Country";
    CountryRow.textContent = vDesk.Locale.Countries.find(Country => Country.Code === Contact.Country.Code)?.Name ?? "";

    /**
     * The company row of the Viewer.
     * @type {HTMLLIElement}
     */
    const CompanyRow = document.createElement("li");
    CompanyRow.className = "Row Company";
    CompanyRow.textContent = `${vDesk.Locale.Contacts.Company}: ${vDesk.Contacts.Companies.find(Company => Company.ID === Contact.Company.ID)?.Name ?? ""}`;

    AddressList.appendChild(StreetRow);
    AddressList.appendChild(CityRow);
    AddressList.appendChild(CountryRow);
    AddressList.appendChild(CompanyRow);

    AddressGroupBox.Add(AddressList);

    /**
     * The Options GroupBox of the Viewer.
     * @type {vDesk.Controls.GroupBox}
     */
    const OptionsGroupBox = new vDesk.Controls.GroupBox(vDesk.Locale.Contacts.ContactOptions);

    /**
     * The Options list of the Viewer.
     * @type {HTMLUListElement}
     */
    const OptionsList = document.createElement("ul");
    OptionsList.className = "Options";

    Contact.Options.forEach(Option => {
        const Row = document.createElement("li");
        const Label = document.createElement("span");
        Label.className = "Label";
        let Text = null;
        switch(Option.Type) {
            case vDesk.Contacts.Contact.Option.Type.Telephone:
                Label.textContent = `${vDesk.Locale.Contacts.PhoneNumber}:`;
                Row.className = "Row PhoneNumber";
                Text = document.createElement("span");
                Text.textContent = Option.Value;
                break;
            case vDesk.Contacts.Contact.Option.Type.Fax:
                Label.textContent = `${vDesk.Locale.Contacts.FaxNumber}:`;
                Row.className = "Row FaxNumber";
                Text = document.createElement("span");
                Text.textContent = Option.Value;
                break;
            case vDesk.Contacts.Contact.Option.Type.Email:
                Label.textContent = `${vDesk.Locale.Contacts.Email}:`;
                Row.className = "Row Email";
                Text = document.createElement("a");
                Text.textContent = Option.Value;
                Text.href = "mailto:" + Option.Value;
                break;
            case vDesk.Contacts.Contact.Option.Type.Website:
                Label.textContent = `${vDesk.Locale.Contacts.Website}:`;
                Row.className = "Row Website";
                Text = document.createElement("a");
                Text.textContent = Option.Value.replace(vDesk.Utils.Expression.URL, "");
                Text.href = vDesk.Utils.Expression.URL.test(Option.Value) ? Option.Value : "http://" + Option.Value;
                Text.target = "blank";
                break;
        }
        Text.className = "Label";

        Row.appendChild(Label);
        Row.appendChild(Text);
        OptionsList.appendChild(Row);
    });

    OptionsGroupBox.Add(OptionsList);

    /**
     * The annotations GroupBox of the Viewer.
     * @type {vDesk.Controls.GroupBox}
     */
    const AnnotationsGroupBox = new vDesk.Controls.GroupBox(vDesk.Locale.Contacts.Annotations);

    /**
     * The annotations text of the Viewer.
     * @type {HTMLDivElement}
     */
    const AnnotationsText = document.createElement("div");
    AnnotationsText.className = "Annotations";
    AnnotationsText.textContent = Contact?.Annotations ?? "";

    AnnotationsGroupBox.Add(AnnotationsText);

    Control.appendChild(NameRow);
    Control.appendChild(AddressGroupBox.Control);
    Control.appendChild(OptionsGroupBox.Control);
    Control.appendChild(AnnotationsGroupBox.Control);
};