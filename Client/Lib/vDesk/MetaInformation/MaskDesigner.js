/**
 * Initializes a new instance of the MaskDesigner class.
 * @class Represents an editor for creating or modifying masks, defining the structure for DataSets.
 * The MaskDesigner is a plugin for remote configuration.
 * @param {Boolean} [Enabled=true] Flag indicating whether the MaskDesigner is enabled.
 * @property {HTMLElement} Control Gets the underlying DomNode.
 * @property {String} Title Gets the title of the MaskDesigner specified by the plugin interface.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the MaskDesigner is enabled.
 * @memberOf vDesk.MetaInformation
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.MaskDesigner = function MaskDesigner(Enabled = true) {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale["MetaInformation"]["MaskDesigner"]
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;

                //Check if the user is allowed to edit masks.
                if(vDesk.User.Permissions["UpdateMask"]) {
                    EditSaveButton.disabled = !Value;
                    MaskList.Enabled = Value;
                    MaskEditor.Enabled = Value;
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Configuration.MaskList#event:select
     */
    const OnSelect = Event => {
        if(Event.detail.item.Mask.ID !== MaskEditor.Mask.ID) {

            //Check if the 'new Mask' entry has been selected.
            if(Event.detail.item.Mask.ID === null) {
                ResetButton.disabled = true;
                DeleteButton.disabled = true;
            } else {
                DeleteButton.disabled = !vDesk.User.Permissions["DeleteMask"];
            }

            //Display the selected Mask.
            MaskEditor.Mask = Event.detail.item.Mask;

        }
    };

    /**
     * Eventhandler that listens on the 'change' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Configuration.Mask.Editor#event:change
     */
    const OnChange = Event => {

        EditSaveButton.disabled = MaskEditor.Mask.Name.length === 0 && !MaskEditor.Changed;
        ResetButton.disabled = !MaskEditor.Changed;

        if(MaskEditor.Changed) {
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Save || vDesk.Visual.Icons.Unknown}")`;
            EditSaveButton.textContent = vDesk.Locale["vDesk"]["Save"];
        }

    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Configuration.Mask.Editor#event:create
     */
    const OnCreate = Event => {
        MaskList.Find(Event.detail.mask.ID).Mask = Event.detail.mask;
        vDesk.MetaInformation.Masks.push(Event.detail.mask);
        DeleteButton.disabled = !vDesk.User.Permissions["DeleteMask"];
        ResetButton.disabled = true;
        MaskList.Add(
            new vDesk.MetaInformation.MaskList.Item(
                new vDesk.MetaInformation.Mask(
                    null,
                    vDesk.Locale["MetaInformation"]["NewMask"]
                )
            )
        );
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Configuration.Mask.Editor#event:update
     */
    const OnUpdate = Event => {
        MaskList.Find(Event.detail.mask.ID).Mask = Event.detail.mask;
        vDesk.MetaInformation.Masks.find(Mask => Mask.ID === Event.detail.mask.ID).Name = Event.detail.mask.Name;
        ResetButton.disabled = true;
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Configuration.Mask.Editor#event:delete
     */
    const OnDelete = Event => {
        vDesk.MetaInformation.Masks.splice(
            vDesk.MetaInformation.Masks.indexOf(vDesk.MetaInformation.Masks.find(Mask => Mask.ID === Event.detail.mask.ID)),
            1
        );
        MaskList.Remove(MaskList.Selected);
        if(MaskList.Items.length > 0) {
            MaskList.Selected = MaskList.Items[0];
        }
        MaskEditor.Mask = (MaskList.Selected || {Mask: new vDesk.MetaInformation.Mask()}).Mask;
        MaskEditor.Enabled = false;
        DeleteButton.disabled = MaskEditor.Mask.ID !== null &&  !vDesk.User.Permissions["DeleteMask"];
        EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
        EditSaveButton.textContent = vDesk.Locale["vDesk"]["Edit"];
        EditSaveButton.disabled = !vDesk.User.Permissions["UpdateMask"];
        ResetButton.disabled = true;
    };

    /**
     * Saves possible made changes.
     */
    const OnClickEditSaveButton = () => {
        if(MaskEditor.Enabled) {
            if(MaskEditor.Changed) {
                MaskEditor.Save();
            }
            MaskEditor.Enabled = false;
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
            EditSaveButton.textContent = vDesk.Locale["vDesk"]["Edit"];
        } else {
            //Enable MaskEditor.
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel || vDesk.Visual.Icons.Unknown}")`;
            EditSaveButton.textContent = vDesk.Locale["vDesk"]["Cancel"];
            MaskEditor.Enabled = true;
        }
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickResetButton = () => {
        MaskEditor.Reset();
        if(MaskEditor.Enabled) {
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel || vDesk.Visual.Icons.Unknown}")`;
            EditSaveButton.textContent = vDesk.Locale["vDesk"]["Cancel"];
        } else {
            EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
            EditSaveButton.textContent = vDesk.Locale["vDesk"]["Edit"];
        }
        EditSaveButton.disabled = false;
        ResetButton.disabled = true;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickDeleteButton = () => {
        if(MaskEditor.Mask.ID !== null && confirm(vDesk.Locale["MetaInformation"]["DeleteMask"])) {
            MaskEditor.Delete();
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "MaskDesigner";
    Control.addEventListener("select", OnSelect, false);
    Control.addEventListener("change", OnChange, false);
    Control.addEventListener("create", OnCreate, false);
    Control.addEventListener("update", OnUpdate, false);
    Control.addEventListener("delete", OnDelete, false);

    /**
     * The MaskList of the MaskDesigner.
     * @type {vDesk.MetaInformation.MaskList}
     */
    const MaskList = vDesk.MetaInformation.MaskList.FromMasks();

    //Check if the user is allowed to create new masks.
    if(vDesk.User.Permissions["CreateMask"]) {
        MaskList.Add(
            new vDesk.MetaInformation.MaskList.Item(
                new vDesk.MetaInformation.Mask(
                    null,
                    vDesk.Locale["MetaInformation"]["NewMask"]
                )
            )
        );
    }

    if(MaskList.Items.length > 0) {
        MaskList.Selected = MaskList.Items[0];
    }
    Control.appendChild(MaskList.Control);

    /**
     * The Mask.Editor of the MaskDesigner.
     * @type {vDesk.MetaInformation.Mask.Editor}
     */
    const MaskEditor = new vDesk.MetaInformation.Mask.Editor((MaskList.Selected || {Mask: new vDesk.MetaInformation.Mask()}).Mask, false);
    Control.appendChild(MaskEditor.Control);



    /**
     * The edit/save button of the MaskDesigner.
     * @type {HTMLButtonElement}
     */
    const EditSaveButton = document.createElement("button");
    EditSaveButton.className = "Button Icon Save";
    EditSaveButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
    EditSaveButton.textContent = vDesk.Locale["vDesk"]["Edit"];
    EditSaveButton.disabled = !vDesk.User.Permissions["UpdateMask"];
    EditSaveButton.addEventListener("click", OnClickEditSaveButton, false);

    /**
     * The reset button of the MaskDesigner.
     * @type {HTMLButtonElement}
     */
    const ResetButton = document.createElement("button");
    ResetButton.className = "Button Icon Reset";
    ResetButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    ResetButton.disabled = true;
    ResetButton.textContent = vDesk.Locale["vDesk"]["ResetChanges"];
    ResetButton.addEventListener("click", OnClickResetButton, false);

    /**
     * The delete button of the MaskDesigner.
     * @type {HTMLButtonElement}
     */
    const DeleteButton = document.createElement("button");
    DeleteButton.className = "Button Icon Reset";
    DeleteButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete || vDesk.Visual.Icons.Unknown}")`;
    DeleteButton.disabled = !vDesk.User.Permissions["DeleteMask"];
    DeleteButton.textContent = vDesk.Locale["vDesk"]["Delete"];
    DeleteButton.addEventListener("click", OnClickDeleteButton, false);

    /**
     * The controls row of the MaskDesigner.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Controls.appendChild(EditSaveButton);
    Controls.appendChild(ResetButton);
    Controls.appendChild(DeleteButton);
    Control.appendChild(Controls);
};

/**
 * Register MaskDesigner as plugin.
 * @type vDesk.MetaInformation.MaskDesigner
 */
vDesk.Configuration.Remote.Plugins.MaskDesigner = vDesk.MetaInformation.MaskDesigner;