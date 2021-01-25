"use strict";
/**
 * Initializes a new instance of the GeneralInfo class.
 * @class Represents a control for displaying general information about an Element.
 * @param {vDesk.Archive.Element} Element Initializes the GeneralInfo with the specified Element.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the IAttribute.
 * @memberOf vDesk.Archive.Attributes
 * @implements vDesk.Archive.IAttribute
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Archive.Attributes.GeneralInfo = function GeneralInfo(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperties(this, {
        Control: {
            get: () => Control
        },
        Title:   {
            get: () => vDesk.Locale.Archive.Details
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "GeneralInfo";

    /**
     * The list containing the general attributes of the element.
     * @type {HTMLUListElement}
     */
    const Attributes = document.createElement("ul");
    Attributes.className = "List Font Dark";

    /**
     * The row containing the name.
     * @type {HTMLLIElement}
     */
    const Name = document.createElement("li");
    Name.className = "Item";
    Name.textContent = `${vDesk.Locale.vDesk.Name}: ${Element.Name}`;
    Attributes.appendChild(Name);

    /**
     * The row containing the owner.
     * @type {HTMLLIElement}
     */
    const Owner = document.createElement("li");
    Owner.className = "Item";
    Owner.textContent = `${vDesk.Locale.Archive.Owner}: ${vDesk.Security.Users.find(User => User.ID === Element.Owner.ID).Name}`;
    Attributes.appendChild(Owner);

    /**
     * The row containing the time of creation.
     * @type {HTMLLIElement}
     */
    const CreationTime = document.createElement("li");
    CreationTime.className = "Item";
    CreationTime.textContent = `${vDesk.Locale.Archive.CreationTime}: ${new Date(Element.CreationTime).toLocaleString()}`;
    Attributes.appendChild(CreationTime);

    /**
     * The row containing the guid.
     * @type {HTMLLIElement}
     */
    const Guid = document.createElement("li");
    Guid.className = "Item";
    Guid.textContent = `Guid: ${Element.Guid}`;
    Attributes.appendChild(Guid);

    /**
     * The row containing the filename.
     * @type {HTMLLIElement}
     */
    const File = document.createElement("li");
    File.className = "Item";
    File.textContent = `${vDesk.Locale.Archive.File}: ${Element?.File ?? Element.Name}`;
    Attributes.appendChild(File);

    /**
     * The row containing the size.
     * @type {HTMLLIElement}
     */
    const Size = document.createElement("li");
    Size.className = "Item";
    Size.textContent = `${vDesk.Locale.Archive.Size}: ${vDesk.Archive.Attributes.GeneralInfo.RoundFileSize(
        Element?.Size ?? 0,
        vDesk.Configuration.Settings?.Local?.Archive?.IEC ?? false
    )}`;
    Attributes.appendChild(Size);

    Control.appendChild(Attributes);
};
/**
 * Utility-method that rounds and formats a specified file-size into a human readable string.
 * @param {Number} Size The size to format.
 * @param {Boolean} [IEC=true] Flag indicating whether the binary based calculation value will be used.
 * @return {string} A formatted string representing a human readable and rounded file-size.
 */
vDesk.Archive.Attributes.GeneralInfo.RoundFileSize = function(Size, IEC = true) {
    const Factor = IEC ? 1024 : 1000;

    if(Size < Factor) {
        return `${Size} Byte`;
    }

    if(Size < (Factor * Factor)) {
        return `${Number(Size / Factor).toFixed(2)} ${IEC ? "KiB" : "KB"}`;
    }

    if(Size < (Factor * Factor * Factor)) {
        return `${Number(Size / (Factor * Factor)).toFixed(2)} ${IEC ? "MiB" : "MB"}`;
    }

    if(Size < (Factor * Factor * Factor * Factor)) {
        return `${Number(Size / (Factor * Factor * Factor)).toFixed(2)} ${IEC ? "GiB" : "GB"}`;
    }

    if(Size < (Factor * Factor * Factor * Factor * Factor)) {
        return `${Number(Size / (Factor * Factor * Factor * Factor)).toFixed(2)} ${IEC ? "TiB" : "TB"}`;
    }
};

vDesk.Archive.Attributes.GeneralInfo.Permission = () => vDesk.User.Permissions.ReadAttributes;

vDesk.Archive.Attributes.GeneralInfo.Implements(vDesk.Archive.IAttribute);
