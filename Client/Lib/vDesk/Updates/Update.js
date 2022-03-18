"use strict";
/**
 * Initializes a new instance of the Update class.
 * @class Class that represents an Update.
 * @param {String} [Package=""] Initializes the Update with the specified name of the Update's target Package.
 * @param {String} [Version=""] Initializes the Update with the specified version.
 * @param {Object<String, String>} [Dependencies={}] Initializes the Update with the specified dependency Packages.
 * @param {String} [Vendor=""] Initializes the Update with the specified vendor.
 * @param {String} [RequiredVersion=""] Initializes the Update with the specified required version.
 * @param {String} [Description=""] Initializes the Update with the specified description.
 * @param {String} [Source=""] Initializes the Update with the specified source.
 * @param {String} [Hash=""] Initializes the Update with the specified SHA256 hash code.
 * @property {String} Package Gets or sets the name of the Update's target Package.
 * @property {String} Version Gets or sets the version of the Update.
 * @property {Object<String, String>} Dependencies Gets or sets the dependencies of the Update.
 * @property {String} Vendor Gets or sets the vendor of the Update.
 * @property {String} Description Gets or sets the description of the Update.
 * @property {String} License Gets or sets the license of the Update.
 * @property {String} LicenseText Gets or sets the license text of the Update.
 * @memberOf vDesk.Updates
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Updates
 */
vDesk.Updates.Update = function Update(
    Package         = "",
    Version         = "",
    Dependencies    = {},
    Vendor          = "",
    RequiredVersion = "",
    Description     = "",
    Source          = "",
    Hash            = ""
) {
    Ensure.Parameter(Package, Type.String, "Package");
    Ensure.Parameter(Version, Type.String, "Version");
    Ensure.Parameter(Dependencies, Type.Object, "Dependencies");
    Ensure.Parameter(Vendor, Type.String, "Vendor");
    Ensure.Parameter(RequiredVersion, Type.String, "RequiredVersion");
    Ensure.Parameter(Description, Type.String, "Description");
    Ensure.Parameter(Source, Type.String, "Source");
    Ensure.Parameter(Hash, Type.String, "Hash");

    Object.defineProperties(this, {
        Control:         {
            enumerable: false,
            get:        () => Control

        },
        Package:         {
            enumerable: true,
            get:        () => PackageText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Package");
                PackageText.textContent = Value;
            }
        },
        Version:         {
            enumerable: true,
            get:        () => VersionText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Version");
                VersionText.textContent = Value;
            }
        },
        RequiredVersion: {
            enumerable: true,
            get:        () => VersionText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "RequiredVersion");
                RequiredVersionText.textContent = Value;
            }
        },
        Dependencies:    {
            enumerable: true,
            get:        () => Dependencies,
            set:        Value => {
                Ensure.Property(Value, Array, "Dependencies");
                Dependencies = Value;

                while(DependenciesList.hasChildNodes()){
                    DependenciesList.removeChild(DependenciesList.lastChild);
                }

                for(const Dependency in Value){
                    const Module = document.createElement("li");
                    Module.className = "Module Font Dark";
                    Module.textContent = `${Dependency} v${Value[Dependency]}`;
                    DependenciesList.appendChild(Module);
                }
            }
        },
        Vendor:          {
            enumerable: true,
            get:        () => VendorText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Vendor");
                VendorText.textContent = Value;
            }
        },
        Description:     {
            enumerable: true,
            get:        () => DescriptionText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Description");
                DescriptionText.textContent = Value;
            }
        },
        Source:          {
            enumerable: true,
            get:        () => SourceText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Source");
                SourceText.textContent = Value;
            }
        },
        Hash:            {
            enumerable: true,
            get:        () => HashText.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Hash");
                HashText.textContent = Value;
            }
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "Update Font";

    /**
     * The name label of the Update.
     * @type {HTMLSpanElement}
     */
    const PackageLabel = document.createElement("span");
    PackageLabel.className = "Label Font Disabled";
    PackageLabel.textContent = vDesk.Locale.Packages.Package;

    /**
     * The name text of the Update.
     * @type {HTMLSpanElement}
     */
    const PackageText = document.createElement("span");
    PackageText.className = "Text Font Dark";
    PackageText.textContent = Package;

    /**
     * The name row of the Update.
     * @type {HTMLLIElement}
     */
    const PackageRow = document.createElement("li");
    PackageRow.className = "Row Package";
    PackageRow.appendChild(PackageLabel);
    PackageRow.appendChild(PackageText);
    Control.appendChild(PackageRow);

    /**
     * The version label of the Update.
     * @type {HTMLSpanElement}
     */
    const VersionLabel = document.createElement("span");
    VersionLabel.className = "Label Font Disabled";
    VersionLabel.textContent = vDesk.Locale.Packages.Version;

    /**
     * The version text of the Update.
     * @type {HTMLSpanElement}
     */
    const VersionText = document.createElement("span");
    VersionText.className = "Text Font Dark";
    VersionText.textContent = Version;

    /**
     * The version row of the Update.
     * @type {HTMLLIElement}
     */
    const VersionRow = document.createElement("li");
    VersionRow.className = "Row Version";
    VersionRow.appendChild(VersionLabel);
    VersionRow.appendChild(VersionText);
    Control.appendChild(VersionRow);

    /**
     * The version label of the Update.
     * @type {HTMLSpanElement}
     */
    const RequiredVersionLabel = document.createElement("span");
    RequiredVersionLabel.className = "Label Font Disabled";
    RequiredVersionLabel.textContent = vDesk.Locale.Updates.RequiredVersion;

    /**
     * The version text of the Update.
     * @type {HTMLSpanElement}
     */
    const RequiredVersionText = document.createElement("span");
    RequiredVersionText.className = "Text Font Dark";
    RequiredVersionText.textContent = RequiredVersion;

    /**
     * The version row of the Update.
     * @type {HTMLLIElement}
     */
    const RequiredVersionRow = document.createElement("li");
    RequiredVersionRow.className = "Row Version";
    RequiredVersionRow.appendChild(RequiredVersionLabel);
    RequiredVersionRow.appendChild(RequiredVersionText);
    Control.appendChild(RequiredVersionRow);

    /**
     * The dependencies label of the Update.
     * @type {HTMLSpanElement}
     */
    const DependenciesLabel = document.createElement("span");
    DependenciesLabel.className = "Label Font Disabled";
    DependenciesLabel.textContent = vDesk.Locale.Packages.Dependencies;

    /**
     * The dependencies list of the Update.
     * @type {HTMLSpanElement}
     */
    const DependenciesList = document.createElement("span");
    DependenciesList.className = "Text Font Dark";
    for(const Dependency in Dependencies){
        const Module = document.createElement("li");
        Module.className = "Module Font Dark";
        Module.textContent = `${Dependency} v${Dependencies[Dependency]}`;
        DependenciesList.appendChild(Module);
    }

    /**
     * The dependencies row of the Update.
     * @type {HTMLLIElement}
     */
    const DependenciesRow = document.createElement("li");
    DependenciesRow.className = "Row Dependencies";
    DependenciesRow.appendChild(DependenciesLabel);
    DependenciesRow.appendChild(DependenciesList);
    Control.appendChild(DependenciesRow);

    /**
     * The vendor label of the Update.
     * @type {HTMLSpanElement}
     */
    const VendorLabel = document.createElement("span");
    VendorLabel.className = "Label Font Disabled";
    VendorLabel.textContent = vDesk.Locale.Packages.Vendor;

    /**
     * The vendor text of the Update.
     * @type {HTMLSpanElement}
     */
    const VendorText = document.createElement("span");
    VendorText.className = "Text Font Dark";
    VendorText.textContent = Vendor;

    /**
     * The vendor row of the Update.
     * @type {HTMLLIElement}
     */
    const VendorRow = document.createElement("li");
    VendorRow.className = "Row Vendor";
    VendorRow.appendChild(VendorLabel);
    VendorRow.appendChild(VendorText);
    Control.appendChild(VendorRow);

    /**
     * The description label of the Update.
     * @type {HTMLSpanElement}
     */
    const DescriptionLabel = document.createElement("span");
    DescriptionLabel.className = "Label Font Disabled";
    DescriptionLabel.textContent = vDesk.Locale.Packages.Description;

    /**
     * The description text of the Update.
     * @type {HTMLParagraphElement}
     */
    const DescriptionText = document.createElement("p");
    DescriptionText.className = "Text Font Dark";
    DescriptionText.textContent = Description;

    /**
     * The description row of the Update.
     * @type {HTMLLIElement}
     */
    const DescriptionRow = document.createElement("li");
    DescriptionRow.className = "Row Description";
    DescriptionRow.appendChild(DescriptionLabel);
    DescriptionRow.appendChild(DescriptionText);
    Control.appendChild(DescriptionRow);

    /**
     * The license label of the Update.
     * @type {HTMLSpanElement}
     */
    const SourceLabel = document.createElement("span");
    SourceLabel.className = "Label Font Disabled";
    SourceLabel.textContent = vDesk.Locale.Updates.Source;

    /**
     * The license text of the Update.
     * @type {HTMLParagraphElement}
     */
    const SourceText = document.createElement("p");
    SourceText.className = "Text Font Dark";
    SourceText.textContent = Source;

    /**
     * The source row of the Update.
     * @type {HTMLLIElement}
     */
    const SourceRow = document.createElement("li");
    SourceRow.className = "Row Source";
    SourceRow.appendChild(SourceLabel);
    SourceRow.appendChild(SourceText);
    Control.appendChild(SourceRow);

    /**
     * The hash label of the Update.
     * @type {HTMLSpanElement}
     */
    const HashLabel = document.createElement("span");
    HashLabel.className = "Label Font Disabled";
    HashLabel.textContent = vDesk.Locale.Updates.Hash;

    /**
     * The license text pre of the Update.
     * @type {HTMLParagraphElement}
     */
    const HashText = document.createElement("p");
    HashText.className = "Text Font Dark";
    HashText.textContent = Hash;

    /**
     * The hash row of the Update.
     * @type {HTMLLIElement}
     */
    const HashRow = document.createElement("li");
    HashRow.className = "Row Hash";
    HashRow.appendChild(HashLabel);
    HashRow.appendChild(HashText);
    Control.appendChild(HashRow);

};

/**
 * Factory method that creates a Package from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Update.
 * @return {vDesk.Updates.Update} A Package filled with the provided data.
 */
vDesk.Updates.Update.FromDataView = function(DataView) {
    return new vDesk.Updates.Update(
        DataView?.Package ?? "",
        DataView?.Version ?? "",
        DataView?.Dependencies ?? [],
        DataView?.Vendor ?? "",
        DataView?.RequiredVersion ?? "",
        DataView?.Description ?? "",
        DataView?.Source ?? "",
        DataView?.Hash ?? ""
    );
};

/**
 * Returns a string representation of the Update.
 * @returns {string}
 */
vDesk.Updates.Update.prototype.toString = function() {
    return `${this.Package} [${this.Version}]`;
}

/**
 * Value indicating local hosted Updates.
 * @constant
 */
vDesk.Updates.Update.Local = "vDesk";
