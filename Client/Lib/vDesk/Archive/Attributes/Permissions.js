"use strict";
/**
 * Initializes a new instance of the Permissions class.
 * @class Represents a control for editing the {@link vDesk.Security.AccessControlList} of an Element.
 * @param {vDesk.Archive.Element} Element Initializes the GeneralInfo with the specified Element.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the IAttribute.
 * @memberOf vDesk.Archive.Attributes
 * @implements vDesk.Archive.IAttribute
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.Attributes.Permissions = function Permissions(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperties(this, {
        Control: {
            get: () => Control
        },
        Title:   {
            get: () => vDesk.Locale.Security.Permissions
        }
    });

    /**
     * Eventhandler that listens on the 'change' event.
     * @listens vDesk.Security.AccessControlList.Editor#event:change
     */
    const OnChange = () => {
        SaveButton.disabled = false;
        ResetButton.disabled = false;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickSaveButton = () => {
        if(AccessControlListEditor.Changed){
            AccessControlListEditor.Save();
        }
        SaveButton.disabled = true;
        ResetButton.disabled = true;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickResetButton = () => {
        AccessControlListEditor.Reset();
        SaveButton.disabled = true;
        ResetButton.disabled = true;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Permissions";
    Control.addEventListener("change", OnChange, false);

    /**
     * The AccessControlListEditor of the Permissions.
     * @type {vDesk.Security.AccessControlList.Editor}
     */
    const AccessControlListEditor = new vDesk.Security.AccessControlList.Editor(Element.AccessControlList, vDesk.Security.User.Current.Permissions.UpdateAccessControlList);
    Element.AccessControlList.Fill(AccessControlList => AccessControlListEditor.AccessControlList = AccessControlList);
    Control.appendChild(AccessControlListEditor.Control);

    /**
     * The save button of the Permissions.
     * @type {HTMLButtonElement}
     */
    const SaveButton = document.createElement("button");
    SaveButton.className = "Button Icon Save BorderDark Font Dark";
    SaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Save}")`;
    SaveButton.textContent = vDesk.Locale.vDesk.Save;
    SaveButton.disabled = true;
    SaveButton.addEventListener("click", OnClickSaveButton, false);

    /**
     * The reset button of the Permissions.
     * @type {HTMLButtonElement}
     */
    const ResetButton = document.createElement("button");
    ResetButton.className = "Button Icon Reset BorderDark Font Dark";
    ResetButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    ResetButton.disabled = true;
    ResetButton.textContent = vDesk.Locale.vDesk.ResetChanges;
    ResetButton.addEventListener("click", OnClickResetButton, false);

    /**
     * The controls of the Permissions.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Controls.appendChild(SaveButton);
    Controls.appendChild(ResetButton);
    Control.appendChild(Controls);
};
vDesk.Archive.Attributes.Permissions.Permission = () => vDesk.Security.User.Current.Permissions.ReadAccessControlList;
vDesk.Archive.Attributes.Permissions.Implements(vDesk.Archive.IAttribute);
