/**
 * @typedef {Object} CommandMap.
 * @property {String} Module The Module of the CommandMap.
 * @property {String} Command The Module of the CommandMap.
 * @property {Object<*>} [Parameters] The parameters of the CommandMap.
 * @property {String} [Ticket] The ticket to use. Can be omitted if the Command doesn't require authentication.
 */
/**
 * Initializes a new instance of the Command class.
 * @class Represents a command and provides functionality for preparing it for execution on the server.
 * The Command maps a key-value-pair object against a specified set of parameters of a called command.
 * @param {CommandMap} CommandMap The name of the module to call.
 * @property {String} Module Gets the name of the Module to execute the Command against.
 * @property {String} Command Gets the name of the Command to execute.
 * @property {FormData} Data Gets the data to pass as parameters.
 * @property {Boolean} Canceled Gets a value indicating whether the execution of the command has been canceled.
 * @memberOf vDesk.Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Move to vDesk.Modules-Namespace.
 */
vDesk.Modules.Command = function Command({Module, Command, Parameters = {}, Ticket} = {}) {

    //Using
    const CGI = vDesk.Modules.Command.CGI;

    /**
     * The validated data of the Command.
     * @type {null|FormData|vDesk.Utils.FormData}
     */
    let Data = null;

    /**
     * Flag indicating whether the execution of the command has been canceled.
     * @type {Boolean}
     */
    let Canceled = false;

    /**
     * The cancel callback that stops the execution of the command.
     * @type {Function}
     */
    let Cancel = () => {};

    Object.defineProperties(this, {
        Module:   {
            enumerable: true,
            get:        () => Module
        },
        Command:  {
            enumerable: true,
            get:        () => Command
        },
        Data:     {
            enumerable: true,
            get:        () => Data
        },
        Cancel:   {
            enumerable: true,
            get:        () => CancelExecution,
            set:        Value => {
                Ensure.Property(Value, Type.Callable, "Cancel");
                Cancel = Value;
            }
        },
        Canceled: {
            enumerable: true,
            get:        () => Canceled
        }
    });

    /**
     * Cancels the execution of the Command.
     */
    const CancelExecution = function() {
        Canceled = true;
        Cancel();
    };
    //Check if the given command is a default command, which has to be known before fetching the modules and their commands from the server.
    switch(Command) {
        case vDesk.Modules.Command.Commands.ReLogin:
            Module = CGI.Module + Module;
            Command = CGI.Command + Command + CGI.Ticket + Ticket;
            break;
        case vDesk.Modules.Command.Commands.Login:
            Module = CGI.Module + Module;
            Command = CGI.Command + Command;
            Data = new vDesk.Utils.FormData();
            Data.append("User", JSON.stringify(Parameters.User));
            Data.append("Password", JSON.stringify(Parameters.Password));
            break;
        case vDesk.Modules.Command.Commands.Logout:
            Module = CGI.Module + Module;
            Command = CGI.Command + Command + CGI.Ticket + Ticket;
            Data = new vDesk.Utils.FormData();
            Data.append("User", JSON.stringify(Parameters.User));
            break;
        default:
            //Fetch the module.
            const ModuleDescriptor = vDesk.Modules.Commands.find(ModuleDescriptor => ModuleDescriptor.Name === Module);
            //const ModuleInstance = vDesk.Modules.Running.find(ModuleDescriptor => ModuleDescriptor.Name === Module);
            //Check if the module exists.
            if(ModuleDescriptor === undefined) {
                throw new TypeError(`Undefined module with name: '${Module}'.`);
            }
            Module = CGI.Module + Module;

            //Fetch the command.
            const CommandDescriptor = ModuleDescriptor.Commands.find(CommandDescriptor => CommandDescriptor.Name === Command);
            //Check if the command exists.
            if(CommandDescriptor === undefined) {
                throw new TypeError(`Undefined command with name: '${Command}' of module '${Module}'.`);
            }
            Command = CGI.Command + Command;

            //Check if the command requires a ticket.
            if(CommandDescriptor.RequireTicket) {
                if(Ticket === undefined) {
                    throw new TypeError(`Missing ticket for command with name: '${Command}' of module '${Module}'.`);
                }
                Command += CGI.Ticket + Ticket;
            }

            Data = CommandDescriptor.Binary ? new FormData() : new vDesk.Utils.FormData();

            //Check if the command awaits parameters.

            //Loop through parameters and check passed arguments for completeness and correct type.
            (CommandDescriptor.Parameters || []).forEach(Parameter => {
                //Check if the parameter has been passed.
                if(Parameters[Parameter.Name] === undefined) {
                    //Check if the Parameter is required.
                    if(!Parameter.Optional) {
                        throw new TypeError(
                            `Missing value for required Parameter '${Parameter.Name}' of Command '${ModuleDescriptor.Name}::${CommandDescriptor.Name}'!`);
                    }
                } else {
                    //Check if the passed value matches the required datatype.
                    if(
                        !Parameter.Nullable
                        && Parameters[Parameter.Name] !== null
                        && !vDesk.Utils.Validate.As(Parameters[Parameter.Name], Parameter.Type)
                    ) {
                        throw new TypeError(
                            `Value of Parameter '${Parameter.Name}' of Command '${ModuleDescriptor.Name}::${CommandDescriptor.Name}' must be type of ${Parameter.Type}, ${Parameters[Parameter.Name].constructor.name || typeof Parameters[Parameter.Name]} given.`);
                    }
                    //Append form data.
                    Data.append(
                        Parameter.Name,
                        (Parameter.Type === "file")
                        ? Parameters[Parameter.Name]
                        : JSON.stringify(Parameters[Parameter.Name])
                    );
                }
            });
    }

};

/**
 * Factory method that creates a Command from a CommandMap.
 * @param {Object} CommandMap The CommandMap to use to create an instance of the Command.
 * @return {vDesk.Modules.Command} An Command created from the specified CommandMap.
 */
vDesk.Modules.Command.FromCommandMap = function(CommandMap) {
    return new vDesk.Modules.Command(CommandMap);
};

/**
 * Convenience method to execute a Command directly after creation against the server.
 * @see vDesk.Connection.Execute
 * @return {Promise<Response>}
 */
vDesk.Modules.Command.prototype.Execute = function() {
    return vDesk.Connection.Execute(this);
};

/**
 * Collection of predefined commands.
 * @readonly
 * @enum {String}
 * @name vDesk.Modules.Command.Commands
 */
vDesk.Modules.Command.Commands = {
    Connect:     "Connect",
    Login:       "Login",
    ReLogin:     "ReLogin",
    Logout:      "Logout",
    GetCommands: "GetCommands"
};

/**
 * Collection of CommonGatewayInterface constants.
 * @constant
 * @type Object
 * @name vDesk.Modules.Command.CGI
 */
vDesk.Modules.Command.CGI = {
    Module:  "&Module=",
    Command: "&Command=",
    Ticket:  "&Ticket="
};

/**
 * Cancels the execution of the command.
 */
vDesk.Modules.Command.prototype.Cancel = function() {
};