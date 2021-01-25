"use strict";
/**
 * Fired if the Package has been uninstalled.
 * @event vDesk.Packages.Package#uninstall
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'uninstall' event.
 * @property {vDesk.Packages.Package} detail.sender The current instance of the Package.
 * @property {Number} detail.name The name of the uninstalled Package.
 */
/**
 * Initializes a new instance of the Package class.
 * @class Class that represents a [...] for [...]. | Class providing functionality for [...].
 *
 * @param {String} [Name=""] Initializes the Package with the specified name.
 * @param {String} [Version=""] Initializes the Package with the specified version.
 * @param {Object<String, String>} [Dependencies={}] Initializes the Package with the specified dependency Packages.
 * @param {String} [Vendor=""] Initializes the Package with the specified vendor.
 * @param {String} [Description=""] Initializes the Package with the specified description.
 * @param {String} [License""] Initializes the Package with the specified license.
 * @param {String} [LicenseText""] Initializes the Package with the specified license text.
 * @property {String} Name Gets or sets the name of the Package.
 * @property {String} Version Gets or sets the version of the Package.
 * @property {Object<String, String>} Dependencies Gets or sets the dependencies of the Package.
 * @property {String} Vendor Gets or sets the vendor of the Package.
 * @property {String} Description Gets or sets the description of the Package.
 * @property {String} License Gets or sets the license of the Package.
 * @property {String} LicenseText Gets or sets the license text of the Package.
 * @memberOf vDesk.Packages
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Packages.Package = function Package(
    Name         = "",
    Version      = "",
    Dependencies = {},
    Vendor       = "",
    Description  = "",
    License      = "",
    LicenseText  = ""
) {
    Ensure.Parameter(Name, Type.String, "Name");
    Ensure.Parameter(Version, Type.String, "Version");
    Ensure.Parameter(Dependencies, Type.Object, "Dependencies");
    Ensure.Parameter(Vendor, Type.String, "Vendor");
    Ensure.Parameter(Description, Type.String, "Description");
    Ensure.Parameter(License, Type.String, "License");
    Ensure.Parameter(LicenseText, Type.String, "LicenseText");

    Object.defineProperties(this, {
        Control:      {
            enumerable: false,
            get:        () => Control

        },
        Name:         {
            enumerable: true,
            get:        () => NameText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                NameText.textContent = Value;
            }
        },
        Version:      {
            enumerable: true,
            get:        () => VersionText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Version");
                VersionText.textContent = Value;
            }
        },
        Dependencies: {
            enumerable: true,
            get:        () => Dependencies,
            set:        Value => {
                Ensure.Property(Value, Array, "Dependencies");
                Dependencies = Value;

                while(DependenciesList.hasChildNodes()) {
                    DependenciesList.removeChild(DependenciesList.lastChild);
                }

                for(const Dependency in Value) {
                    const Module = document.createElement("li");
                    Module.className = "Module Font Dark";
                    Module.textContent = `${Dependency} v${Value[Dependency]}`;
                    DependenciesList.appendChild(Module);
                }
            }
        },
        Vendor:       {
            enumerable: true,
            get:        () => VendorText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Vendor");
                VendorText.textContent = Value;
            }
        },
        Description:  {
            enumerable: true,
            get:        () => DescriptionText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Description");
                DescriptionText.textContent = Value;
            }
        },
        License:      {
            enumerable: true,
            get:        () => LicenseNode.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "License");
                LicenseNode.textContent = Value;
            }
        },
        LicenseText:  {
            enumerable: true,
            get:        () => LicenseTextNode.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "LicenseText");
                LicenseTextNode.textContent = Value;
            }
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "Package Font";

    /**
     * The name label of the Package.
     * @type {HTMLSpanElement}
     */
    const NameLabel = document.createElement("span");
    NameLabel.className = "Label Font Disabled";
    NameLabel.textContent = vDesk.Locale.Packages.Package;

    /**
     * The name text of the Package.
     * @type {HTMLSpanElement}
     */
    const NameText = document.createElement("span");
    NameText.className = "Text Font Dark";
    NameText.textContent = Name;

    /**
     * The name row of the Package.
     * @type {HTMLLIElement}
     */
    const NameRow = document.createElement("li");
    NameRow.className = "Row Name";
    NameRow.appendChild(NameLabel);
    NameRow.appendChild(NameText);
    Control.appendChild(NameRow);

    /**
     * The version label of the Package.
     * @type {HTMLSpanElement}
     */
    const VersionLabel = document.createElement("span");
    VersionLabel.className = "Label Font Disabled";
    VersionLabel.textContent = vDesk.Locale.Packages.Version;

    /**
     * The version text of the Package.
     * @type {HTMLSpanElement}
     */
    const VersionText = document.createElement("span");
    VersionText.className = "Text Font Dark";
    VersionText.textContent = Version;

    /**
     * The version row of the Package.
     * @type {HTMLLIElement}
     */
    const VersionRow = document.createElement("li");
    VersionRow.className = "Row Version";
    VersionRow.appendChild(VersionLabel);
    VersionRow.appendChild(VersionText);
    Control.appendChild(VersionRow);

    /**
     * The dependencies label of the Package.
     * @type {HTMLSpanElement}
     */
    const DependenciesLabel = document.createElement("span");
    DependenciesLabel.className = "Label Font Disabled";
    DependenciesLabel.textContent = vDesk.Locale.Packages.Dependencies;

    /**
     * The dependencies list of the Package.
     * @type {HTMLSpanElement}
     */
    const DependenciesList = document.createElement("span");
    DependenciesList.className = "Text Font Dark";
    for(const Dependency in Dependencies) {
        const Module = document.createElement("li");
        Module.className = "Module Font Dark";
        Module.textContent = `${Dependency} v${Dependencies[Dependency]}`;
        DependenciesList.appendChild(Module);
    }

    /**
     * The dependencies row of the Package.
     * @type {HTMLLIElement}
     */
    const DependenciesRow = document.createElement("li");
    DependenciesRow.className = "Row Name";
    DependenciesRow.appendChild(DependenciesLabel);
    DependenciesRow.appendChild(DependenciesList);
    Control.appendChild(DependenciesRow);

    /**
     * The vendor label of the Package.
     * @type {HTMLSpanElement}
     */
    const VendorLabel = document.createElement("span");
    VendorLabel.className = "Label Font Disabled";
    VendorLabel.textContent = vDesk.Locale.Packages.Vendor;

    /**
     * The vendor text of the Package.
     * @type {HTMLSpanElement}
     */
    const VendorText = document.createElement("span");
    VendorText.className = "Text Font Dark";
    VendorText.textContent = Vendor;

    /**
     * The vendor row of the Package.
     * @type {HTMLLIElement}
     */
    const VendorRow = document.createElement("li");
    VendorRow.className = "Row Vendor";
    VendorRow.appendChild(VendorLabel);
    VendorRow.appendChild(VendorText);
    Control.appendChild(VendorRow);

    /**
     * The description label of the Package.
     * @type {HTMLSpanElement}
     */
    const DescriptionLabel = document.createElement("span");
    DescriptionLabel.className = "Label Font Disabled";
    DescriptionLabel.textContent = vDesk.Locale.Packages.Description;

    /**
     * The description text of the Package.
     * @type {HTMLParagraphElement}
     */
    const DescriptionText = document.createElement("p");
    DescriptionText.className = "Text Font Dark";
    DescriptionText.textContent = Description;

    /**
     * The description row of the Package.
     * @type {HTMLLIElement}
     */
    const DescriptionRow = document.createElement("li");
    DescriptionRow.className = "Row Description";
    DescriptionRow.appendChild(DescriptionLabel);
    DescriptionRow.appendChild(DescriptionText);
    Control.appendChild(DescriptionRow);

    /**
     * The license label of the Package.
     * @type {HTMLSpanElement}
     */
    const LicenseLabel = document.createElement("span");
    LicenseLabel.className = "Label Font Disabled";
    LicenseLabel.textContent = "License"; // vDesk.Locale.Packages.Description;

    /**
     * The license text of the Package.
     * @type {HTMLParagraphElement}
     */
    const LicenseNode = document.createElement("p");
    LicenseNode.className = "Text Font Dark";
    LicenseNode.textContent = License;

    /**
     * The license row of the Package.
     * @type {HTMLLIElement}
     */
    const LicenseRow = document.createElement("li");
    LicenseRow.className = "Row Description";
    LicenseRow.appendChild(LicenseLabel);
    LicenseRow.appendChild(LicenseNode);
    Control.appendChild(LicenseRow);

    /**
     * The license text pre of the Package.
     * @type {HTMLPreElement}
     */
    const LicenseTextNode = document.createElement("pre");
    LicenseTextNode.textContent = LicenseText;

    /**
     * The license text GroupBox of the Package.
     * @type {vDesk.Controls.GroupBox}
     */
    const LicenseTextGroupBox = new vDesk.Controls.GroupBox("License Text", [LicenseTextNode], true, false);

    /**
     * The license text row of the Package.
     * @type {HTMLLIElement}
     */
    const LicenseTextRow = document.createElement("li");
    LicenseTextRow.className = "Row Description";
    LicenseTextRow.appendChild(LicenseTextGroupBox.Control);
    Control.appendChild(LicenseTextRow);

    /**
     * Uninstalls the Package.
     * @fires vDesk.Packages.Package#uninstall
     */
    this.Uninstall = function() {
        if(Name !== null) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Packages",
                        Command:    "Uninstall",
                        Parameters: {Name},
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        new vDesk.Events.BubblingEvent("uninstall", {
                            sender: this,
                            name:   Name
                        }).Dispatch(Control);
                    }
                }
            );
        }
    }
};

/**
 * Factory method that creates a Package from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Package.
 * @return {vDesk.Packages.Package} A Package filled with the provided data.
 */
vDesk.Packages.Package.FromDataView = function(DataView) {
    return new vDesk.Packages.Package(
        DataView?.Name ?? "",
        DataView?.Version ?? "",
        DataView?.Dependencies ?? [],
        DataView?.Vendor ?? "",
        DataView?.Description ?? "",
        DataView?.License ?? "",
        DataView?.LicenseText ?? ""
    );
};