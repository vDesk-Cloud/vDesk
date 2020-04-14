"use strict";
/**
 * Fired if the current edited AccessControlList of the Editor has been changed.
 * @event vDesk.Security.AccessControlList.Editor#change
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'change' event.
 * @property {vDesk.Security.AccessControlList.Editor} detail.sender The current instance of the Editor.
 */
/**
 * Fired if a new AccessControlList has been created.
 * @event vDesk.Security.AccessControlList.Editor#create
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'create' event.
 * @property {vDesk.Security.AccessControlList.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Security.AccessControlList} detail.accesscontrollist The newly created AccessControlList.
 */
/**
 * Fired if the current edited AccessControlList of the Editor has been updated.
 * @event vDesk.Security.AccessControlList.Editor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Security.AccessControlList.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Security.AccessControlList} detail.accesscontrollist The updated AccessControlList.
 */
/**
 * Initializes a new instance of the Editor class.
 * @class Represents an editor for creating and modifying AccessControlLists.
 * @param {vDesk.Security.AccessControlList} AccessControlList Initializes the Editor with the specified AccessControlList.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Editor is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.AccessControlList} AccessControlList Gets or sets the current edited AccessControlList of the Editor.
 * @property {Boolean} Changed Gets a value indicating whether the Entries of the current edited AccessControlList of the Editor have been changed.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @memberOf vDesk.Security.AccessControlList
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.AccessControlList.Editor = function Editor(AccessControlList, Enabled = true) {
    Ensure.Parameter(AccessControlList, vDesk.Security.AccessControlList, "AccessControlList");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the AccessControlList has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    /**
     * The added Entries of the AccessControlList of the Editor.
     * @type Array<vDesk.Security.AccessControlList.Entry>
     */
    let Added = [];

    /**
     * The updated Entries of the AccessControlList of the Editor.
     * @type Array<vDesk.Security.AccessControlList.Entry>
     */
    let Updated = [];

    /**
     * The deleted Entries of the AccessControlList of the Editor.
     * @type Array<vDesk.Security.AccessControlList.Entry>
     */
    let Deleted = [];

    /**
     * The previous AccessControlList to restore any changed values.
     * @type {vDesk.Security.AccessControlList}
     */
    let PreviousAccessControlList = vDesk.Security.AccessControlList.FromDataView(AccessControlList);

    /**
     * The current selected Entry or Item.
     * @type vDesk.Security.AccessControlList.Entry|vDesk.Security.UserGroupList.Item
     */
    let SelectedElement = null;

    Object.defineProperties(this, {
        Control:           {
            enumerable: true,
            get:        () => Control
        },
        AccessControlList: {
            enumerable: true,
            get:        () => AccessControlList,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.AccessControlList, "AccessControlList");

                Control.replaceChild(Value.Control, AccessControlList.Control);
                AccessControlList = Value;
                PreviousAccessControlList = vDesk.Security.AccessControlList.FromDataView(Value);

                //Clear list and refill it.
                UserGroupList.Fill(AccessControlList);
                Added = [];
                Updated = [];
                Deleted = [];
                SelectedElement = null;

                Left.disabled = true;
                Right.disabled = true;
            }
        },
        Enabled:           {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                AccessControlList.Enabled = Value;
                UserGroupList.Enabled = Value;
            }
        },
        Changed:           {
            enumerable: true,
            get:        () => Changed
        }
    });

    /**
     * Eventhandler that listens on the 'drop' event.
     * @listens vDesk.Security.AccessControlList#event:drop
     * @fires vDesk.Security.AccessControlList.Editor#change
     * @param {CustomEvent} Event
     */
    const OnDrop = Event => {
        const Entry = new vDesk.Security.AccessControlList.Entry(
            null,
            Event.detail.item.Group,
            Event.detail.item.User
        );

        Added.push(Entry);
        UserGroupList.Remove(Event.detail.item);
        AccessControlList.Add(Entry);

        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Security.AccessControlList.Entry#event:select
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        if(SelectedElement !== null) {
            SelectedElement.Selected = false;
        }
        SelectedElement = Event.detail.sender;
        SelectedElement.Selected = true;

        if(Event.detail.sender instanceof vDesk.Security.AccessControlList.Entry) {
            ToggleArrowLeftButton(false);
            ToggleArrowRightButton(true);
        } else if(Event.detail.sender instanceof vDesk.Security.UserGroupList.Item) {
            ToggleArrowLeftButton(true);
            ToggleArrowRightButton(false);
        }
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Security.AccessControlList.Entry#event:update
     * @fires vDesk.Security.AccessControlList.Editor#change
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        //Check if the entry has an ID and has not been updated before.
        if(Event.detail.sender.ID !== null && !~Updated.indexOf(Event.detail.sender)) {
            Updated.push(Event.detail.sender);
        }
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.Security.AccessControlList.Entry#event:delete
     * @fires vDesk.Security.AccessControlList.Editor#change
     * @param {CustomEvent} Event
     */
    const OnDelete = Event => {
        Event.stopPropagation();
        //Check if the deleted entry is not virtual
        if(Event.detail.sender.ID !== null) {
            //Check if the entry has been updated before.
            const Index = Updated.indexOf(Event.detail.sender);
            if(~Index) {
                Updated.splice(Index, 1);
            }
            Deleted.push(Event.detail.sender);
        } else {
            //Else it is an added one, so remove it from the added list.
            Added.splice(Added.indexOf(Event.detail.sender), 1);
        }

        //Append the deleted Entry to the UserGroupList.
        UserGroupList.Add(
            new vDesk.Security.UserGroupList.Item(
                Event.detail.sender.Group,
                Event.detail.sender.User
            )
        );
        AccessControlList.Remove(Event.detail.sender);
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * Removes the current selected UserGroupListItem from the UserGroupList and appends it to the AccessControlList.
     */
    const OnClickLeft = () => {

        const Entry = new vDesk.Security.AccessControlList.Entry(null, SelectedElement.Group, SelectedElement.User);
        Added.push(Entry);

        //Remove the item from the UserGroupList.
        UserGroupList.Remove(SelectedElement);

        //Append the new entry to the AccessControlList.
        AccessControlList.Add(Entry);

        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
        SelectedElement = Entry;
        SelectedElement.Selected = true;
        ToggleArrowLeftButton(false);
        ToggleArrowRightButton(true);
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * Removes the current selected Entry from the AccessControlList and appends it to the UserGroupList.
     */
    const OnClickRight = () => {
        //Check if the deleted entry is not virtual
        if(SelectedElement.ID !== null) {

            //Check if the entry has been updated before.
            const Index = Updated.indexOf(SelectedElement);
            if(~Index) {
                Updated.splice(Index, 1);
            }
            Deleted.push(SelectedElement);
            Changed = true;
        } else {
            //Else it is an added one, so remove it from the added list.
            Added.splice(Added.indexOf(SelectedElement), 1);
        }

        //Append the deleted Entry to the UserGroupList.
        const Item = new vDesk.Security.UserGroupList.Item(
            SelectedElement.Group,
            SelectedElement.User
        );
        UserGroupList.Add(Item);
        AccessControlList.Remove(SelectedElement);
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
        SelectedElement = Item;
        SelectedElement.Selected = true;
        ToggleArrowLeftButton(true);
        ToggleArrowRightButton(false);
    };

    /**
     * Toggles the state of the left arrow button.
     * @param {Boolean} State The state to set.
     */
    const ToggleArrowLeftButton = State => Left.disabled = Enabled ? !State : false;

    /**
     * Toggles the state of the right arrow button.
     * @param {Boolean} State The state to set.
     */
    const ToggleArrowRightButton = State => Right.disabled = Enabled ? !State : false;

    /**
     * Saves all applied changes.
     * @fires vDesk.Security.AccessControlList.Editor#event:update
     */
    this.Save = function() {
        if(
            Changed
            && AccessControlList.ID !== null
            && (
                Added.length > 0
                || Updated.length > 0
                || Deleted.length > 0
            )
        ) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Security",
                        Command:    "UpdateAccessControlList",
                        Parameters: {
                            ID:     AccessControlList.ID,
                            Add:    Added.map(Entry => ({
                                Group:  Entry.Group.ID || null,
                                User:   Entry.User.ID || null,
                                Read:   Entry.Read,
                                Write:  Entry.Write,
                                Delete: Entry.Delete
                            })),
                            Update: Updated.map(Entry => ({
                                ID:     Entry.ID,
                                Read:   Entry.Read,
                                Write:  Entry.Write,
                                Delete: Entry.Delete
                            })),
                            Delete: Deleted.map(Entry => Entry.ID)
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        this.AccessControlList = vDesk.Security.AccessControlList.FromDataView(Response.Data);
                        Changed = false;
                        Control.removeEventListener("update", OnUpdate, false);
                        new vDesk.Events.BubblingEvent("update", {
                            sender:            this,
                            accesscontrollist: AccessControlList
                        }).Dispatch(Control);
                        Control.addEventListener("update", OnUpdate, false);
                    }
                }
            );
        }
    };

    /**
     * Discards any applied changes.
     */
    this.Reset = () => this.AccessControlList = PreviousAccessControlList;

    /**
     * Merges a specified AccessControlList into the current edited AccessControlList of the Editor.
     * @param {vDesk.Security.AccessControlList} AccessControlList The AccessControlList to merge.
     */
    this.Merge = function(AccessControlList) {
        Ensure.Parameter(AccessControlList, vDesk.Security.AccessControlList, "AccessControlList");

        //Reset changes first.
        //this.Reset();
        //@todo Refactor this shit!

        //Copy ID if the current edited AccessControlList is virtual.
        this.AccessControlList.ID = this.AccessControlList.ID || AccessControlList.ID;

        //Merge Entries of the passed AccessControlList into the current AccessControlList.
        this.AccessControlList.Entries.forEach(Entry => {
            //Check if the Entry exists in the current specified AccessControlList.
            const VirtualEntry = AccessControlList.Entries.find(
                VirtualEntry => VirtualEntry.Group.ID === Entry.Group.ID
                                && VirtualEntry.User.ID === Entry.User.ID
            );
            if(VirtualEntry !== undefined) {

                //Check if the permissions differ.
                if(
                    VirtualEntry.Read !== Entry.Read
                    || VirtualEntry.Write !== Entry.Write
                    || VirtualEntry.Delete !== Entry.Delete
                ) {
                    //Mark non virtual Entries as updated.
                    if(Entry.ID !== null) {
                        //Copy permissions and mark it as updated.
                        Entry.Read = VirtualEntry.Read;
                        Entry.Write = VirtualEntry.Write;
                        Entry.Delete = VirtualEntry.Delete;

                    } else {
                        //Copy ID if the Entry is virtual.
                        Entry.ID = Entry.ID || VirtualEntry.ID;
                    }
                    Updated.push(Entry);
                }
            } else {
                //Otherwise mark non virtual Entries as deleted.
                if(Entry.ID !== null) {
                    Deleted.push(Entry);
                }
                this.AccessControlList.Remove(Entry);
            }
        });

        //Add unique Entries from the specified AccessControlList to the current edited AccessControlList.
        AccessControlList.forEach(VirtualEntry => {
            if(
                this.AccessControlList.Entries.find(
                    Entry => Entry.Group.ID === VirtualEntry.Group.ID
                             && Entry.User.ID === VirtualEntry.User.ID
                ) === undefined
            ) {
                const Entry = vDesk.Security.AccessControlList.Entry.FromDataView(VirtualEntry);
                Entry.ID = null;
                this.AccessControlList.Add(Entry);
                Added.push(Entry);
            }
        });

        Changed = true;

        //Clear and refill the UserGroupList.
        UserGroupList.Fill(this.AccessControlList);

    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "AccessControlListEditor";
    Control.appendChild(AccessControlList.Control);
    Control.addEventListener("drop", OnDrop, false);
    Control.addEventListener("select", OnSelect, false);
    Control.addEventListener("update", OnUpdate, false);
    Control.addEventListener("delete", OnDelete, false);

    /**
     * The UserGroupList of the Editor.
     * @type vDesk.Security.UserGroupList
     */
    const UserGroupList = vDesk.Security.UserGroupList.FromACL(AccessControlList);
    Control.appendChild(UserGroupList.Control);

    /**
     * The arrow left button of the Editor.
     * @type {HTMLButtonElement}
     */
    const Left = document.createElement("button");
    Left.className = "Button Arrow Left";
    Left.textContent = "🡰";
    Left.disabled = true;
    Left.addEventListener("click", OnClickLeft, false);

    /**
     * The arrow right button of the Editor.
     * @type {HTMLButtonElement}
     */
    const Right = document.createElement("button");
    Right.className = "Button Arrow Right";
    Right.textContent = "🡲";
    Right.disabled = true;
    Right.addEventListener("click", OnClickRight, false);

    /**
     * The row containing the arrow buttons of the Editor.
     * @type {HTMLDivElement}
     */
    const Arrows = document.createElement("div");
    Arrows.className = "Arrows";
    Arrows.appendChild(Left);
    Arrows.appendChild(Right);

    Control.appendChild(Arrows);

};