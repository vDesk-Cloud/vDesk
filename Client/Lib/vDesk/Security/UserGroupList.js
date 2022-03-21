"use strict";
/**
 * Initializes a new instance of the UserGroupList class.
 * @class Represents a list of users and groups.
 * @param {Array<vDesk.Security.UserGroupList.Item>} [Items=[]] Initializes the UserGroupList with the specified set of Items.
 * @param {Boolean} [Enabled=true] Flag indicating whether the UserGroupList is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Security.UserGroupList.Item>} Items Gets or sets the Items of the UserGroupList.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the UserGroupList is enabled.
 * @memberOf vDesk.Security
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Security
 */
vDesk.Security.UserGroupList = function UserGroupList(Items = [], Enabled = true) {
    Ensure.Parameter(Items, Array, "Items");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Items:   {
            enumerable: true,
            get:        () => Items,
            set:        Value => {
                Ensure.Property(Value, Array, "Items");

                //Remove elements.
                this.Clear();
                Items = Value;

                //Append new entries.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.Security.UserGroupList.Item, "Item");
                    Item.Enabled = Enabled;
                    Fragment.appendChild(Item.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Name");
                Enabled = Value;
                Items.forEach(Item => Item.Enabled = Value);
            }
        }
    });

    /**
     * Adds an Item to the UserGroupList.
     * @param {vDesk.Security.UserGroupList.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Security.UserGroupList.Item, "Item");
        //Check if the entry doesn't already exist.
        if(Item.User.ID !== null && this.FindByUser(Item.User) === null){
            Items.push(Item);
            Control.appendChild(Item.Control);
        }else if(Item.Group.ID !== null && this.FindByGroup(Item.Group) === null){
            Items.push(Item);
            Control.appendChild(Item.Control);
        }
    };

    /**
     * Returns the Item of the UserGroupList which matches the specified User.
     * @param {vDesk.Security.User} User The User of the Item to search.
     * @return {vDesk.Security.UserGroupList.Item|null} The found Item; otherwise, null.
     */
    this.FindByUser = function(User) {
        Ensure.Parameter(User, vDesk.Security.User, "User");
        return Items.find(Item => Item.User.ID === User.ID) ?? null;
    };

    /**
     * Returns the Item of the UserGroupList which matches the specified Group.
     * @param {vDesk.Security.Group} Group The Group of the Item to search.
     * @return {vDesk.Security.UserGroupList.Item|null} The found Item; otherwise, null.
     */
    this.FindByGroup = function(Group) {
        Ensure.Parameter(Group, vDesk.Security.Group, "Group");
        return Items.find(Item => Item.Group.ID === Group.ID) ?? null;
    };

    /**
     * Removes all Items from the UserGroupList.
     */
    this.Clear = function() {
        Items.forEach(Item => Control.removeChild(Item.Control));
        Items = [];
    };

    /**
     * Removes an Item from the UserGroupList.
     * @param {vDesk.Security.UserGroupList.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Security.UserGroupList.Item, "Item");
        const Index = Items.indexOf(Item);
        //Check if the item to remove exists.
        if(~Index){
            Control.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * Fills the UserGroupList with Items which are not represent in the specified AccessControlList.
     * @param AccessControlList The AccessControlList to apply.
     */
    this.Fill = function(AccessControlList) {
        Ensure.Parameter(AccessControlList, vDesk.Security.AccessControlList, "AccessControlList");
        this.Items = [
            ...vDesk.Security.Users.filter(User => AccessControlList.FindByUser(User) === null)
                .map(User => new vDesk.Security.UserGroupList.Item(new vDesk.Security.Group(), User)),
            ...vDesk.Security.Groups.filter(Group => AccessControlList.FindByGroup(Group) === null)
                .map(Group => new vDesk.Security.UserGroupList.Item(Group, new vDesk.Security.User()))
        ];
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "UserGroupList BorderDark";

    /**
     * The header item of the UserGroupList.
     * @type {HTMLLIElement}
     */
    const Header = document.createElement("li");
    Header.className = "Header Foreground Font Light BorderDark";

    /**
     * The name span of the UserGroupList.
     * @type {HTMLSpanElement}
     */
    const Name = document.createElement("span");
    Name.className = "Name Label";
    Name.textContent = vDesk.Locale.Security.UserGroup;

    Header.appendChild(Name);

    Control.appendChild(Header);

    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.Security.UserGroupList.Item, "Item");
        Item.Enabled = Enabled;
        Control.appendChild(Item.Control);
    });
};

/**
 * Factory method that creates a UserGroupList from an AccessControlList that only contains Items which are not represent in the specified AccessControlList.
 * @param {vDesk.Security.AccessControlList} AccessControlList The AccessControlList to use to create an instance of the UserGroupList.
 * @return {vDesk.Security.UserGroupList} An UserGroupList filled with the non existent Items of the specified AccessControlList.
 */
vDesk.Security.UserGroupList.FromACL = function(AccessControlList) {
    Ensure.Parameter(AccessControlList, vDesk.Security.AccessControlList, "AccessControlList");
    return new vDesk.Security.UserGroupList(
        [
            ...vDesk.Security.Users.filter(User => AccessControlList.FindByUser(User) === null)
                .map(User => new vDesk.Security.UserGroupList.Item(new vDesk.Security.Group(), User)),
            ...vDesk.Security.Groups.filter(Group => AccessControlList.FindByGroup(Group) === null)
                .map(Group => new vDesk.Security.UserGroupList.Item(Group, new vDesk.Security.User()))
        ]
    );
};