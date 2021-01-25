"use strict";
/**
 * Initializes a new instance of the Viewer class.
 * @class Represents a viewer-control for displaying the data of a company.
 * @param {vDesk.Contacts.Company} Company The Company whose data to be displayed.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @memberOf vDesk.Contacts
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Contacts.Company.Viewer = function Viewer(Company) {
    Ensure.Parameter(Company, vDesk.Contacts.Company, "Company");

    Object.defineProperty(this, "Control", {
        enumerable: true,
        get:        () => Control
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "CompanyViewer Font";

    /**
     * The name row of the Viewer.
     * @type {HTMLHeadingElement}
     */
    const NameRow = document.createElement("h2");
    NameRow.className = "Row Name Font Dark";
    NameRow.textContent = Company.Name;

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
    StreetRow.textContent = `${Company.Street} ${Company.HouseNumber}`;

    /**
     * The city row of the Viewer.
     * @type {HTMLLIElement}
     */
    const CityRow = document.createElement("li");
    CityRow.className = "Row City";
    CityRow.textContent = `${Company.ZipCode} ${Company.City}`;

    /**
     * The country row of the Viewer.
     * @type {HTMLLIElement}
     */
    const CountryRow = document.createElement("li");
    CountryRow.className = "Row Country";
    CountryRow.textContent = vDesk.Locale.Countries.find(Country => Country.Code === Company.Country.Code)?.Name ?? "";

    AddressList.appendChild(StreetRow);
    AddressList.appendChild(CityRow);
    AddressList.appendChild(CountryRow);
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

    /**
     * The phone number row of the Viewer.
     * @type {HTMLLIElement}
     */
    const PhoneNumberRow = document.createElement("li");
    PhoneNumberRow.className = "Row PhoneNumber";

    /**
     * The phone number label of the Viewer.
     * @type {HTMLSpanElement}
     */
    const PhoneNumberLabel = document.createElement("span");
    PhoneNumberLabel.className = "Label";
    PhoneNumberLabel.textContent = `${vDesk.Locale.Contacts.PhoneNumber}:`;

    /**
     * The phone number text of the Viewer.
     * @type {HTMLSpanElement}
     */
    const PhoneNumberText = document.createElement("span");
    PhoneNumberText.className = "Text";
    PhoneNumberText.textContent = Company.PhoneNumber;

    PhoneNumberRow.appendChild(PhoneNumberLabel);
    PhoneNumberRow.appendChild(PhoneNumberText);

    /**
     * The fax number row of the Viewer.
     * @type {HTMLLIElement}
     */
    const FaxNumberRow = document.createElement("li");
    FaxNumberRow.className = "Row FaxNumber";

    /**
     * The fax number label of the Viewer.
     * @type {HTMLSpanElement}
     */
    const FaxNumberLabel = document.createElement("span");
    FaxNumberLabel.className = "Label";
    FaxNumberLabel.textContent = `${vDesk.Locale.Contacts.FaxNumber}:`;

    /**
     * The fax number text of the Viewer.
     * @type {HTMLSpanElement}
     */
    const FaxNumberText = document.createElement("span");
    FaxNumberText.className = "Text";
    FaxNumberText.textContent = Company.FaxNumber;

    FaxNumberRow.appendChild(FaxNumberLabel);
    FaxNumberRow.appendChild(FaxNumberText);

    /**
     * The email row of the Viewer.
     * @type {HTMLLIElement}
     */
    const EmailRow = document.createElement("li");
    EmailRow.className = "Row Email";

    /**
     * The email label of the Viewer.
     * @type {HTMLSpanElement}
     */
    const EmailLabel = document.createElement("span");
    EmailLabel.className = "Label";
    EmailLabel.textContent = `${vDesk.Locale.Contacts.Email}:`;

    /**
     * The email text of the Viewer.
     * @type HTMLAnchorElement
     */
    const EmailLink = document.createElement("a");
    EmailLink.className = "Text";
    EmailLink.textContent = Company.Email;
    EmailLink.href = "mailto:" + Company.Email;

    EmailRow.appendChild(EmailLabel);
    EmailRow.appendChild(EmailLink);

    /**
     * The website row of the Viewer.
     * @type {HTMLLIElement}
     */
    const WebsiteRow = document.createElement("li");
    WebsiteRow.className = "Row Website";

    /**
     * The website label of the Viewer.
     * @type {HTMLSpanElement}
     */
    const WebsiteLabel = document.createElement("span");
    WebsiteLabel.className = "Label";
    WebsiteLabel.textContent = `${vDesk.Locale.Contacts.Website}:`;

    /**
     * The website text of the Viewer.
     * @type HTMLAnchorElement
     */
    const WebsiteLink = document.createElement("a");
    WebsiteLink.className = "Text";
    WebsiteLink.textContent = Company.Website;
    WebsiteLink.href = Company.Website;
    WebsiteLink.target = "blank";

    WebsiteRow.appendChild(WebsiteLabel);
    WebsiteRow.appendChild(WebsiteLink);

    OptionsList.appendChild(PhoneNumberRow);
    OptionsList.appendChild(FaxNumberRow);
    OptionsList.appendChild(EmailRow);
    OptionsList.appendChild(WebsiteRow);

    OptionsGroupBox.Add(OptionsList);

    Control.appendChild(NameRow);
    Control.appendChild(AddressGroupBox.Control);
    Control.appendChild(OptionsGroupBox.Control);
};