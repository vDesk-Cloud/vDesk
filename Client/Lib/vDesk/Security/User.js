"use strict";
/**
 * Initializes a new instance of the User class.
 * @class Represents a user.
 * @param {Number} [ID=null] Initializes the User with the specified ID.
 * @param {String} [Name=""] Initializes the User with the specified name.
 * @param {String} [Locale="DE"] Initializes the User with the specified locale.
 * @param {String} [Ticket=""] Initializes the User with the specified session ticket.
 * @param {?String} [Email=null] Initializes the User with the specified email address.
 * @param {?Boolean} [Active=true] Flag indicating whether the User is active.
 * @param {?Number} [FailedLoginCount=null] Initializes the User with the specified amount of failed login attempts.
 * @param {Array<vDesk.Security.Group>} Memberships Initializes the User with the specified set of membership Groups.
 * @param {Object<Boolean>} [Permissions={}] Initializes the User with the specified enumeration of permissions.
 * @property {Number} ID Gets or sets the ID of the User.
 * @property {String} Name Gets or sets the name of the User.
 * @property {String} Locale Gets or sets the locale of the User.
 * @property {?String} Email Gets or sets the email address of the User.
 * @property {?Boolean} Active Gets or sets a value indicating whether the User is active.
 * @property {?Number} FailedLoginCount Gets or sets amount of failed login attempts of the User.
 * @property {String} Ticket Gets or sets the session ticket of the User.
 * @property {Object<Boolean>} Permissions Gets or sets the rights of the User.
 * @memberOf vDesk.Security
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.User = function User(
    ID               = null,
    Name             = "",
    Locale           = "DE",
    Ticket           = "",
    Email            = "",
    Active           = true,
    FailedLoginCount = 0,
    Memberships      = [],
    Permissions      = {}
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Name, Type.String, "Name");
    Ensure.Parameter(Locale, Type.String, "Locale");
    Ensure.Parameter(Ticket, Type.String, "Ticket");
    Ensure.Parameter(Email, Type.String, "Email");
    Ensure.Parameter(Active, Type.Boolean, "Active");
    Ensure.Parameter(FailedLoginCount, Type.Number, "FailedLoginCount");
    Ensure.Parameter(Memberships, Array, "Memberships");
    Ensure.Parameter(Permissions, Type.Object, "Permissions");

    if(ID === vDesk.Security.User.System) {
        Object.keys(Permissions).forEach(Permission => Permissions[Permission] = true);
    }

    Object.defineProperties(this, {
        ID:               {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Name:             {
            enumerable: true,
            get:        () => Name,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                Name = Value;
            }
        },
        Locale:           {
            enumerable: true,
            get:        () => Locale,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Locale");
                Locale = Value;
            }
        },
        Ticket:           {
            enumerable: true,
            get:        () => Ticket,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Ticket");
                Ticket = Value;
            }
        },
        Email:            {
            enumerable: true,
            get:        () => Email,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Email");
                Email = Value;
            }
        },
        Active:           {
            enumerable: true,
            get:        () => Active,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Active");
                Active = Value;
            }
        },
        FailedLoginCount: {
            enumerable: true,
            get:        () => FailedLoginCount,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "FailedLoginCount");
                FailedLoginCount = Value;
            }
        },
        Memberships:      {
            enumerable: true,
            get:        () => Memberships,
            set:        Value => {
                Ensure.Property(Value, Array, "Memberships");
                Memberships = Value;
            }
        },
        Permissions:      {
            enumerable: true,
            get:        () => Permissions,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Permissions");
                Permissions = Value;
                if(ID === vDesk.Security.User.System) {
                    Object.keys(Permissions).forEach(Permission => Permissions[Permission] = true);
                }
            }
        }
    });
};

/**
 * Factory method that creates an User from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the User.
 * @return {vDesk.Security.User} An User filled with the provided data.
 */
vDesk.Security.User.FromDataView = function(DataView) {
    return new vDesk.Security.User(
        DataView?.ID ?? null,
        DataView?.Name ?? "",
        DataView?.Locale ?? "DE",
        DataView?.Ticket ?? "",
        DataView?.Email ?? "",
        DataView?.Active ?? true,
        DataView?.FailedLoginCount ?? 0,
        DataView?.Memberships?.map(vDesk.Security.Group.FromDataView) ?? [],
        DataView?.Permissions ?? {}
    );
};

/**
 * The ID of the system User.
 * @type {Number}
 * @const
 */
vDesk.Security.User.System = 1;