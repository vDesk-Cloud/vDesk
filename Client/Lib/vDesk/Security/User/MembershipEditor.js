"use strict";
/**
 * Fired if the Group-memberships of the current edited User of the MembershipEditor has been changed.
 * @event vDesk.Security.User.MembershipEditor#change
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'change' event.
 * @property {vDesk.Security.User.MembershipEditor} detail.sender The current instance of the MembershipEditor.
 */
/**
 * Initializes a new instance of the MembershipEditor class.
 * @class Represents an editor for administrating the group-memberships of an user.
 * @param {vDesk.Security.User} User Initializes the MembershipEditor with the specified User to edit.
 * @param {Boolean} [Enabled=false] Flag indicating whether the MembershipEditor is enabled.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {User} User Gets or sets the user to edit whose memberships.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the MembershipEditor is enabled.
 * @property {Boolean} Changed Gets a value indicating whether the memberships of the current user of the MembershipEditor has been modified.
 * @memberOf vDesk.Security.User
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Security
 */
vDesk.Security.User.MembershipEditor = function MembershipEditor(User, Enabled = false) {
    Ensure.Parameter(User, vDesk.Security.User, "User");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The initial state of the current edited User of the Editor.
     * @type {vDesk.Security.User}
     */
    let PreviousUser = vDesk.Security.User.FromDataView(User);

    /**
     * Flag indicating whether the user of the MembershipEditor has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    /**
     * The added Groups of the MembershipEditor.
     * @type {Array<vDesk.Security.Group>}
     */
    let Added = [];

    /**
     * The deleted Groups of the MembershipEditor.
     * @type {Array<vDesk.Security.Group>}
     */
    let Deleted = [];

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => GroupBox.Control
        },
        User:    {
            enumerable: true,
            get:        () => User,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "User");
                Added = [];
                Deleted = [];
                User = Value;
                PreviousUser = vDesk.Security.User.FromDataView(Value);
                MembershipList.Clear();
                GroupList.Clear();

                vDesk.Security.Groups.forEach(Group => {
                    if(Value.Memberships.find(ID => ID === Group.ID) !== undefined || Group.ID === vDesk.Security.Group.Everyone){
                        MembershipList.Add(new vDesk.Security.GroupList.Item(Group, true, Enabled && Group.ID !== vDesk.Security.Group.Everyone));
                    }else{
                        GroupList.Add(new vDesk.Security.GroupList.Item(Group, true, Enabled));
                    }
                });
                Left.disabled = true;
                Right.disabled = true;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value && vDesk.Security.User.Current.Permissions.UpdateUser;
                MembershipList.Enabled = Value;
                GroupList.Enabled = Value;
                Left.disabled = !Value || GroupList.Selected === null;
                Right.disabled = !Value || MembershipList.Selected === null;
            }
        },
        Changed: {
            enumerable: true,
            get:        () => Changed
        }
    });

    /**
     * Eventhandler that listens on the 'drop' event.
     * @listens vDesk.Security.GroupList#event:drop
     * @fires vDesk.Security.User.MembershipEditor#change
     * @param {CustomEvent} Event
     */
    const OnDropMembershipList = Event => {
        if(!~Added.indexOf(Event.detail.item.Group) && !User.Memberships.some(Group => Group === Event.detail.item.Group.ID)){
            Added.push(Event.detail.item.Group);
        }
        Event.stopPropagation();
        GroupList.Remove(Event.detail.item);
        GroupList.Selected = null;
        MembershipList.Add(Event.detail.item);
        MembershipList.Selected = Event.detail.item;
        Left.disabled = true;
        Right.disabled = false;
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(GroupBox.Control);
    };

    /**
     * Eventhandler that listens on the 'drop' event.
     * @listens vDesk.Security.GroupList#event:drop
     * @fires vDesk.Security.User.MembershipEditor#change
     * @param {CustomEvent} Event
     */
    const OnDropGroupList = Event => {
        Event.stopPropagation();
        if(!~Deleted.indexOf(Event.detail.item.Group) && User.Memberships.some(Group => Group === Event.detail.item.Group.ID)){
            Deleted.push(Event.detail.item.Group);
        }
        MembershipList.Remove(Event.detail.item);
        MembershipList.Selected = null;
        GroupList.Add(Event.detail.item);
        GroupList.Selected = Event.detail.item;
        Left.disabled = false;
        Right.disabled = true;
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(GroupBox.Control);
    };

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Security.GroupList#event:select
     * @param {CustomEvent} Event
     */
    const OnSelectMembershipList = Event => {
        Event.stopPropagation();
        GroupList.Selected = null;
        if(Event.detail.item.Group.ID !== vDesk.Security.Group.Everyone){
            Left.disabled = true;
            Right.disabled = false;
        }else{
            Left.disabled = true;
            Right.disabled = true;
        }
    };

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Security.GroupList#event:select
     * @param {CustomEvent} Event
     */
    const OnSelectGroupList = Event => {
        Event.stopPropagation();
        MembershipList.Selected = null;
        Left.disabled = false;
        Right.disabled = true;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.User.MembershipEditor#change
     */
    const OnClickLeft = () => {
        const Item = GroupList.Selected;
        GroupList.Remove(Item);
        MembershipList.Add(Item);
        GroupList.Selected = null;
        MembershipList.Selected = Item;
        if(!~Added.indexOf(Item.Group) && !User.Memberships.some(Group => Group === Item.Group.ID)){
            Added.push(Item.Group);
        }
        Left.disabled = true;
        Right.disabled = false;
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(GroupBox.Control);
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.User.MembershipEditor#change
     */
    const OnClickRight = () => {
        const Item = MembershipList.Selected;
        MembershipList.Remove(Item);
        GroupList.Add(Item);
        MembershipList.Selected = null;
        GroupList.Selected = Item;
        if(!~Deleted.indexOf(Item.Group) && User.Memberships.some(Group => Group === Item.Group.ID)){
            Deleted.push(Item.Group);
        }
        Left.disabled = false;
        Right.disabled = true;
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(GroupBox.Control);
    };

    /**
     * Saves all made changes on the memberships of the current edited user.
     */
    this.Save = function() {
        if(User.ID !== null){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Security",
                        Command:    "SetMemberships",
                        Parameters: {
                            ID:     User.ID,
                            Add:    Added.map(Group => Group.ID),
                            Delete: Deleted.map(Group => Group.ID)
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        User.Memberships = MembershipList.Items.map(Item => Item.Group.ID);

                        //Update the permissions of the current logged in User.
                        if(User.ID === vDesk.Security.User.Current.ID){
                            vDesk.Connection.Send(
                                new vDesk.Modules.Command(
                                    {
                                        Module:  "Security",
                                        Command: "ReLogin",
                                        Ticket:  vDesk.Security.User.Current.Ticket
                                    }
                                ),
                                Response => vDesk.Security.User.Current = vDesk.User = vDesk.Security.User.FromDataView(Response.Data)
                            );
                        }
                        Changed = false;
                        Added = [];
                        Deleted = [];
                    }
                }
            );
        }
    };

    /**
     * Resets all made changes on the memberships of the current edited user.
     */
    this.Reset = () => this.User = PreviousUser;

    /**
     * The Membership GroupList of the MembershipEditor.
     * @type {vDesk.Security.GroupList}
     */
    const MembershipList = new vDesk.Security.GroupList();
    MembershipList.Control.addEventListener("drop", OnDropMembershipList, false);
    MembershipList.Control.addEventListener("select", OnSelectMembershipList, false);

    /**
     * The GroupList of the MembershipEditor.
     * @type {vDesk.Security.GroupList}
     */
    const GroupList = new vDesk.Security.GroupList([], true);
    GroupList.Control.addEventListener("drop", OnDropGroupList, false);
    GroupList.Control.addEventListener("select", OnSelectGroupList, false);

    /**
     * The left arrow button of the MembershipEditor.
     * @type {HTMLButtonElement}
     */
    const Left = document.createElement("button");
    Left.className = "Button Arrow Left";
    Left.textContent = "🡰";
    Left.disabled = true;
    Left.addEventListener("click", OnClickLeft, false);

    /**
     * The right arrow button of the MembershipEditor.
     * @type {HTMLButtonElement}
     */
    const Right = document.createElement("button");
    Right.className = "Button Arrow Right";
    Right.textContent = "🡲";
    Right.disabled = true;
    Right.addEventListener("click", OnClickRight, false);

    /**
     * The ArrowButton Row of the MembershipEditor.
     * @type {HTMLDivElement}
     */
    const Arrows = document.createElement("div");
    Arrows.className = "Arrows";
    Arrows.appendChild(Left);
    Arrows.appendChild(Right);

    //Check if an user has been passed.
    vDesk.Security.Groups.forEach(Group => {
        if(User.Memberships.find(Membership => Membership.ID === Group.ID) !== undefined || Group.ID === vDesk.Security.Group.Everyone){
            MembershipList.Add(new vDesk.Security.GroupList.Item(Group, true, Enabled && Group.ID !== vDesk.Security.Group.Everyone));
        }else{
            GroupList.Add(new vDesk.Security.GroupList.Item(Group, true, Enabled));
        }
    });

    /**
     * The GroupBox of the MembershipEditor.
     * @type {vDesk.Controls.GroupBox}
     */
    const GroupBox = new vDesk.Controls.GroupBox(
        vDesk.Locale.Security.Memberships,
        [
            MembershipList.Control,
            GroupList.Control,
            Arrows
        ]
    );
    GroupBox.Control.classList.add("MembershipEditor");

};