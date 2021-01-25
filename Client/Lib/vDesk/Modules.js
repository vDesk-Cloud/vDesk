"use strict";
/**
 * Namespace that contains Module related Classes.
 * @namespace Modules
 * @memberOf vDesk
 */
vDesk.Modules = new Proxy(
    {

        /**
         * Collection of server side installed Modules and Commands.
         * @todo Rename to "Installed".
         * @name vDesk.Modules.Commands
         * @type {Array}
         */
        Commands: [
            {
                Name:     "Modules",
                Commands: [
                    {
                        Name:          "Connect",
                        RequireTicket: false,
                        Binary:        false,
                        Parameters:    [
                            {
                                Name:     "Version",
                                Type:     Type.String,
                                Optional: false,
                                Nullable: false
                            }
                        ]
                    },
                    {
                        Name:          "GetCommands",
                        RequireTicket: true,
                        Binary:        false,
                        Parameters:    []
                    }
                ]
            }
        ],

        /**
         * The current running Modules of vDesk.
         * @name vDesk.Modules.Running
         * @type {Object<vDesk.Modules.IModule>}
         */
        Running: {},

        /**
         * Runs a specified Module.
         * @name vDesk.Modules.Run
         * @param {String} Module The name of the Module to run.
         */
        Run(Module) {
            Ensure.Parameter(Module, Type.String, "Module");
            if(Modules[Module] === undefined) {
                throw new TypeError(`Module '${Module}' doesn't exist!`);
            }
            this.Running[Module] = new Modules[Module];
        },

        /**
         * Runs all installed Modules.
         * @name vDesk.Modules.RunAll
         * @type {Function}
         */
        RunAll() {
            for(const Module in Modules) {
                this.Running[Module] = new Modules[Module];
            }
        }
    },
    {get: (Modules, Property) => Modules?.[Property] ?? Modules.Running?.[Property] ?? Modules.Run(Property)}
);
vDesk.Load.Modules = {
    Status: "Loading modules",
    Load() {
        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Modules",
                    Command:    "GetCommands",
                    Parameters: {},
                    Ticket:     vDesk.User.Ticket
                }
            )
        );
        if(Response.Status) {
            vDesk.Modules.Commands = Response.Data;
        } else {
            console.log("Error while initializing Module Collection:" + Response.Data);
        }
    }
};
vDesk.Unload.Modules = {
    Status: "Unloading modules",
    Unload() {
        vDesk.Modules.Commands = [{
            Name:     "Modules",
            Commands: [
                {
                    Name:          "Connect",
                    RequireTicket: false,
                    Binary:        false,
                    Parameters:    [
                        {
                            Name:     "Version",
                            Type:     Type.String,
                            Optional: false,
                            Nullable: false
                        }
                    ]
                },
                {
                    Name:          "GetCommands",
                    RequireTicket: true,
                    Binary:        false,
                    Parameters:    []
                }
            ]
        }];
        vDesk.Modules.Running = {};
    }
};