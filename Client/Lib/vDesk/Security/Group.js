"use strict";
/**
 * Initializes a new instance of the Group class.
 * @class Represents a group of Users.
 * @param {Number} [ID=null] Initializes the Group with the specified ID.
 * @param {String} [Name=""] Initializes the Group with the specified name.
 * @param {Object<Boolean>} [Permissions={}] Initializes the Group with the specified set of permissions.
 * @property {Number} ID Gets or sets the ID of the Group.
 * @property {String} Name Gets or sets the name of the Group.
 * @property {Object<Boolean>} Permissions Gets or sets the permissions of the Group.
 * @memberOf vDesk.Security
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.Group = function Group(ID = null, Name = "", Permissions = {}) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Name, Type.String, "Name");
    Ensure.Parameter(Permissions, Type.Object, "Permissions");

    Object.defineProperties(this, {
        ID:          {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Name:        {
            enumerable: true,
            get:        () => ID === vDesk.Security.Group.Everyone ? vDesk.Locale["Security"]["Everyone"] : Name,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                Name = Value;
            }
        },
        Permissions: {
            enumerable: true,
            get:        () => Permissions,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Permissions");
                Permissions = Value;
            }
        }
    });

};

/**
 * Factory method that creates a Group from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Group.
 * @return {vDesk.Security.Group} A Group filled with the provided data.
 */
vDesk.Security.Group.FromDataView = function(DataView) {
    return new vDesk.Security.Group(
        DataView.ID || null,
        DataView.Name || "",
        DataView.Permissions || {}
    );
};

/**
 * The ID of the everyone Group.
 * @type {Number}
 * @const
 */
vDesk.Security.Group.Everyone = 1;