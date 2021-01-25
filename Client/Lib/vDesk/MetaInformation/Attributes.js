"use strict";
/**
 * Initializes a new instance of the Attributes class.
 * @class Represents a control for editing meta data about an Element.
 * @param {vDesk.Archive.Element} Element Initializes the GeneralInfo with the specified Element.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the IAttribute.
 * @memberOf vDesk.MetaInformation
 * @implements vDesk.Archive.IAttribute
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.Attributes = function Attributes(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperties(this, {
        Control: {
            get: () => Control
        },
        Title:   {
            get: () => vDesk.Locale.MetaInformation.MetaData
        }
    });

    /**
     * Eventhandler that listens on the 'select' event and Clears any existing datasets and displays a new and empty dataset according to the captured mask.
     * @param {CustomEvent} Event
     * @listens vDesk.MetaInformation.MaskList#event:select
     */
    const OnSelect = Event => {
        if(
            DataSetEditor.DataSet !== null
            && DataSetEditor.DataSet.ID !== null
            && confirm(vDesk.Locale.MetaInformation.ChangeMask)
        ) {
            ResetButton.disabled = false;
        }
        DataSetEditor.DataSet = new vDesk.MetaInformation.DataSet(Event.detail.mask);
    };

    /**
     * Eventhandler that listens on the 'change' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Configuration.Mask.Editor#event:change
     */
    const OnChange = Event => {

        EditSaveButton.disabled = !DataSetEditor.DataSet.Valid && !DataSetEditor.Changed;
        ResetButton.disabled = !DataSetEditor.Changed;

        if(DataSetEditor.Changed) {
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Save}")`;
            EditSaveButton.textContent = vDesk.Locale.vDesk.Save;
        }

    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.DataSet.Editor#event:create
     */
    const OnCreate = Event => {
        DeleteButton.disabled = !vDesk.User.Permissions.DeletDataSet;
        ResetButton.disabled = true;
    };

    /**
     * Saves possible made changes.
     */
    const OnClickEditSaveButton = () => {
        if(DataSetEditor.Enabled) {
            if(DataSetEditor.Changed) {
                DataSetEditor.Save();
            } else {
                DataSetEditor.Reset();
            }
            DataSetEditor.Enabled = false;
            MaskList.Enabled = false;
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
            EditSaveButton.textContent = vDesk.Locale.vDesk.Edit;
        } else {
            //Enable MaskEditor.
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel}")`;
            EditSaveButton.textContent = vDesk.Locale.vDesk.Cancel;
            DataSetEditor.Enabled = true;
            MaskList.Enabled = true;
        }
        DeleteButton.disabled = !vDesk.User.Permissions.DeleteDataSet || DataSetEditor.DataSet === null || DataSetEditor.DataSet.ID === null;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickResetButton = () => {
        DataSetEditor.Reset();
        if(DataSetEditor.Enabled) {
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel}")`;
            EditSaveButton.textContent = vDesk.Locale.vDesk.Cancel;
        } else {
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
            EditSaveButton.textContent = vDesk.Locale.vDesk.Edit;
        }
        EditSaveButton.disabled = false;
        EditSaveButton.textContent = vDesk.Locale.vDesk.Edit;
        ResetButton.disabled = true;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickDeleteButton = () => {
        if(DataSetEditor.DataSet !== null && DataSetEditor.DataSet.ID !== null && confirm(
            vDesk.Locale.MetaInformation.DeleteDataSet)) {
            DataSetEditor.Delete();
            DeleteButton.disabled = true;
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Attributes";
    Control.addEventListener("select", OnSelect, false);
    Control.addEventListener("create", OnCreate, false);
    Control.addEventListener("change", OnChange, false);

    /**
     * The MaskList of the Attributes.
     * @type {vDesk.MetaInformation.MaskList}
     */
    const MaskList = vDesk.MetaInformation.MaskList.FromMasks();
    MaskList.Enabled = false;
    Control.appendChild(MaskList.Control);

    /**
     * The Editor of the Attributes.
     * {vDesk.MetaInformation.DataSet.Editor}
     */
    const DataSetEditor = vDesk.MetaInformation.DataSet.Editor.FromElement(Element);
    Control.appendChild(DataSetEditor.Control);

    /**
     * The controls container of the Attributes.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";

    /**
     * The edit/save button of the Attributes.
     * @type {HTMLButtonElement}
     */
    const EditSaveButton = document.createElement("button");
    EditSaveButton.className = "Button Icon Save";
    EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
    EditSaveButton.textContent = vDesk.Locale.vDesk.Edit;
    EditSaveButton.disabled = !vDesk.User.Permissions.UpdateMask;
    EditSaveButton.addEventListener("click", OnClickEditSaveButton, false);
    Controls.appendChild(EditSaveButton);

    /**
     * The reset button of the Attributes.
     * @type {HTMLButtonElement}
     */
    const ResetButton = document.createElement("button");
    ResetButton.className = "Button Icon Reset";
    ResetButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    ResetButton.disabled = true;
    ResetButton.textContent = vDesk.Locale.vDesk.ResetChanges;
    ResetButton.addEventListener("click", OnClickResetButton, false);
    Controls.appendChild(ResetButton);

    /**
     * The delete button of the Attributes.
     * @type {HTMLButtonElement}
     */
    const DeleteButton = document.createElement("button");
    DeleteButton.className = "Button Icon Delete";
    DeleteButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete}")`;
    DeleteButton.disabled = !vDesk.User.Permissions.DeleteDataSet || DataSetEditor.DataSet === null;
    DeleteButton.textContent = vDesk.Locale.vDesk.Delete;
    DeleteButton.addEventListener("click", OnClickDeleteButton, false);
    Controls.appendChild(DeleteButton);

    Control.appendChild(Controls);
};
vDesk.MetaInformation.Attributes.Permission = () => vDesk.User.Permissions.ReadDataSet;
vDesk.MetaInformation.Attributes.Implements(vDesk.Archive.IAttribute);
vDesk.Archive.Attributes.MetaInformation = vDesk.MetaInformation.Attributes;