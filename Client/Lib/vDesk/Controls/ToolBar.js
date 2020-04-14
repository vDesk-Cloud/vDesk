"use strict";
/**
 * Initializes a new instance of the ToolBar.
 * @class Represents a Toolbar.
 * @param {Array<vDesk.Controls.ToolBar.Group>} [Groups=[]] Initializes the ToolBar with the specified set of Groups.
 * @property {HTMLElement} Control Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Controls.ToolBar.Group>} Groups Gets or sets the toolbargroups of the toolbar.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.ToolBar = function ToolBar(Groups = []) {
    Ensure.Parameter(Groups, Array, "Groups");

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Groups:  {
            enumerable: true,
            get:        () => Groups,
            set:        Value => {
                Ensure.Property(Value, Array, "Groups");
                this.Clear();
                Groups = Value;
                const Fragment = document.createDocumentFragment();
                Value.forEach(Group => {
                    Ensure.Parameter(Group, vDesk.Controls.ToolBar.Group, "Group");
                    Fragment.appendChild(Group.Control);
                });
                Control.appendChild(Fragment);
            }
        }
    });

    /**
     * Adds a Group to the ToolBar.
     * @param {vDesk.Controls.ToolBar.Group} Group The Group to add.
     */
    this.Add = function(Group) {
        Ensure.Parameter(Group, vDesk.Controls.ToolBar.Group, "Group");
        Control.appendChild(Group.Control);
        Groups.push(Group);
    };

    /**
     * Removes a Group from the ToolBar.
     * @param {vDesk.Controls.ToolBar.Group} Group The Group to remove.
     */
    this.Remove = function(Group) {
        Ensure.Parameter(Group, vDesk.Controls.ToolBar.Group, "Group");
        const Index = Groups.indexOf(Group);
        if(~Index) {
            Control.removeChild(Group.Control);
            Groups.splice(Index, 1);
        }
    };

    /**
     * Clears the ToolBar.
     */
    this.Clear = function() {
        Groups.forEach(Group => Control.removeChild(Group.Control));
        Groups = [];
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLElement}
     */
    const Control = document.createElement("header");
    Control.className = "ToolBar";

    //Loop through passed items and add them to the control.
    Groups.forEach(Group => Control.appendChild(Group.Control));

};