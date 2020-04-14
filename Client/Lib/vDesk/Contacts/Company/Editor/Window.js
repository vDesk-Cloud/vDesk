"use strict";
/**
 * Initializes a new instance of the Window class.
 * @class Represents an Window for modifying or creating Companies.
 * @param {vDesk.Contacts.Company} Company Initializes the Window with the specified Company to edit.
 * @extends vDesk.Controls.Window
 * @memberOf vDesk.Contacts.Company.Editor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Contacts.Company.Editor.Window = function Window(Company) {
    Ensure.Property(Company, vDesk.Contacts.Company, "Company");

    this.Extends(vDesk.Controls.Window);

    /**
     * Eventhandler that listens on the 'change' event.
     * @listens vDesk.Contacts.Company.Editor#event:change
     */
    const OnChange = () => {
        ResetItem.Enabled = true;
        SaveItem.Enabled = true;
    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @listens vDesk.Contacts.Company.Editor#event:create
     */
    const OnCreate = () => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
        DeleteItem.Enabled = true;
        this.Title = vDesk.Locale["Contacts"]["EditContact"];
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Contacts.Company.Editor#event:update
     */
    const OnUpdate = () => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.Contacts.Company.Editor#event:delete
     */
    const OnDelete = () => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
        DeleteItem.Enabled = false;
        this.Title = vDesk.Locale["Contacts"]["NewCompany"];
        CompanyEditor.Company = new vDesk.Contacts.Company();
    };

    /**
     * The save Item of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const SaveItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["vDesk"]["Save"],
        vDesk.Visual.Icons.Save,
        false,
        () => {
            if(CompanyEditor.Changed) {
                CompanyEditor.Save();
            }
        }
    );

    /**
     * The reset Item of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ResetItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["vDesk"]["ResetChanges"],
        vDesk.Visual.Icons.Refresh,
        false,
        () => {
            CompanyEditor.Reset();
            ResetItem.Enabled = false;
        }
    );

    /**
     * The delete Item of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const DeleteItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["vDesk"]["Delete"],
        vDesk.Visual.Icons.Delete,
        Company.ID !== null && vDesk.User.Permissions["DeleteCompany"],
        () => CompanyEditor.Delete()
    );

    /**
     * The toolbar of the Window.
     * @type {vDesk.Controls.ToolBar}
     */
    const ToolBar = new vDesk.Controls.ToolBar(
        [
            new vDesk.Controls.ToolBar.Group(
                vDesk.Locale["Contacts"]["Company"],
                [
                    SaveItem,
                    ResetItem,
                    DeleteItem
                ]
            )
        ]
    );

    /**
     * The Company of the Window.
     * @type vDesk.Contacts.Company.Editor
     */
    const CompanyEditor = new vDesk.Contacts.Company.Editor(Company, true);

    this.Title = Company.ID !== null ? vDesk.Locale["Contacts"]["EditCompany"] : vDesk.Locale["Contacts"]["NewCompany"];
    this.Icon = vDesk.Visual.Icons.Contacts.Company;
    this.Content.appendChild(ToolBar.Control);
    this.Content.appendChild(CompanyEditor.Control);
    this.Content.addEventListener("change", OnChange, false);
    this.Content.addEventListener("create", OnCreate, false);
    this.Content.addEventListener("update", OnUpdate, false);
    this.Content.addEventListener("delete", OnDelete, false);
    this.Control.classList.add("CompanyEditorWindow");
    this.Height = 445;
};