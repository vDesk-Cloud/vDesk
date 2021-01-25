"use strict";
/**
 * Initializes a new instance of the WorkSpace class.
 * @class Represents the central container of the client.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Modules.IVisualModule>} Modules Gets or sets the Modules of the WorkSpace.
 * @property {vDesk.Modules.IVisualModule} Module Gets or sets the current loaded Module of the WorkSpace.
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.WorkSpace = (function WorkSpace() {

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.ModuleList.Item#event:select
     */
    const OnSelect = Event => {
        Container.removeChild(ModuleList.Selected.Module.Control);
        ModuleList.Selected.Module.Unload();
        ModuleList.Selected = Event.detail.sender;
        ModuleList.Selected.Module.Load();
        Container.appendChild(ModuleList.Selected.Module.Control);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLElement}
     */
    const Control = document.createElement("article");
    Control.className = "WorkSpace Background";

    /**
     * The header of the WorkSpace.
     * @type {HTMLElement}
     */
    const Header = document.createElement("header");
    Header.className = "Header Foreground Font Light";

    /**
     * The PackageList of the WorkSpace.
     * @type {null|vDesk.ModuleList}
     */
    let ModuleList = null;

    /**
     * The Module container of the WorkSpace.
     * @type {HTMLDivElement}
     */
    const Container = document.createElement("div");
    Container.className = "Container";

    return {
        get Modules() {
            return ModuleList.Items.map(Item => Item.Module);
        },
        set Modules(Value) {
            Ensure.Parameter(Value, Array, "Modules");
            this.Clear();
            ModuleList.Items = Value.filter(Module => Module instanceof vDesk.Modules.IVisualModule)
                .map(Module => new vDesk.ModuleList.Item(Module));
            if(ModuleList.Selected !== null) {
                ModuleList.Selected.Module.Load();
                Container.appendChild(ModuleList.Selected.Module.Control);
            }
        },
        get Module() {
            return ModuleList.Selected;
        },
        set Module(Value) {
            Ensure.Parameter(Value, vDesk.Modules.IVisualModule, "Module");
            if(ModuleList.Selected !== null) {
                ModuleList.Selected.Module.Unload();
                Container.removeChild(ModuleList.Selected.Module.Control);
            }
            Value.Load();
            ModuleList.Selected = ModuleList.Find(Value);
            Container.appendChild(ModuleList.Selected.Module.Control);
        },
        Load() {
            if(ModuleList === null) {
                ModuleList = new vDesk.ModuleList();
                ModuleList.Control.addEventListener("select", OnSelect, false);
                Control.appendChild(Header);
                Control.appendChild(ModuleList.Control);
                Control.appendChild(Container);
            }
            document.body.appendChild(Control);
        },
        Clear() {
            while(Container.hasChildNodes()) {
                Container.removeChild(Container.lastChild);
            }
            ModuleList.Clear();
        },
        Unload() {
            document.body.removeChild(Control);
        }
    };
})();