"use strict";
/**
 * Initializes a new instance of the Status class.
 * @class Represents plugin for displaying general status information about the system.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the Status-plugin.
 * @memberOf vDesk.Configuration.Remote.Plugins
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Modules
 */
vDesk.Modules.Status = function Status() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.vDesk.Status
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Modules Status";

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Modules",
                Command:    "Status",
                Parameters: {},
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        ),
        Response => {
            if(Response.Status){
                Response.Data.forEach((Status, Module) => {
                    const List = document.createElement("ul");
                    Status.forEach((Value, Key) => {
                        const Item = document.createElement("li");
                        Item.textContent = `${vDesk.Locale[Module][Key]}: ${Value}`;
                        List.appendChild(Item);
                    });
                    const GroupBox = new vDesk.Controls.GroupBox(Module, [List]);
                    Control.appendChild(GroupBox.Control);
                });
            }else{
                alert(Response.Data);
            }
        }
    );
};

vDesk.Load.Status = {
    Status: "Registering Status plugin",
    Load:   () => (vDesk?.Configuration?.Remote?.Plugins ?? {}).Status = vDesk.Modules.Status
};