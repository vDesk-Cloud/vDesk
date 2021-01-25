/**
 * vDesk base namespace.
 * @namespace vDesk
 */
const vDesk = (function() {

    /**
     * The LoginDialog of the Client.
     * @type {vDesk.LoginDialog}
     */
    let LoginDialog = null;

    /**
     * The Clipboard of the Client.
     * @type {vDesk.Clipboard}
     */
    let Clipboard = null;

    return {

        /**
         * Represents the actual logged in User.
         * @type {Object|vDesk.Security.User}
         * @memberOf vDesk
         */
        User: {
            ID:          null,
            Name:        "",
            Locale:      "DE",
            Email:       null,
            Permissions: {}
        },

        /**
         * Contains execution related classes.
         * @namespace Load
         * @memberOf vDesk
         */
        Load: {},

        /**
         * Contains execution related classes.
         * @namespace Unload
         * @memberOf vDesk
         */
        Unload: {},
        Start() {
            vDesk.Header.Load();
            vDesk.WorkSpace.Load();
            vDesk.Footer.Load();

            window.addEventListener(
                "logout",
                () => {
                    this.Stop();
                    this.Start();
                },
                {once: true}
            );
            window.addEventListener("beforeunload", () => this.Stop(), {once: true});

            window.addEventListener("login", () => this.Run(), {once: true, capture: true});
            LoginDialog = new vDesk.LoginDialog();
            LoginDialog.Show();
        },
        Run() {

            vDesk.Header.Clear();
            vDesk.WorkSpace.Clear();
            vDesk.Footer.Clear();

            vDesk.Header.Menu.Add(new vDesk.Menu.Item(vDesk.User.Name, vDesk.Visual.Icons.Security.User));

            //Execute load routines.
            Object.values(this.Load).forEach(Routine => {
                LoginDialog.Status = Routine.Status;
                Routine.Load();
            });

            //Check if the user is allowed to see the settings of the system.
            //@todo Consider implementing an API for the MainMenu. Use manual addition through Header.MainMenu.Add() until then.
            if(vDesk.User.Permissions.ReadSettings) {
                vDesk.Header.MainMenu.Add(
                    new vDesk.MainMenu.Item(
                        vDesk.Locale.Configuration.Administration,
                        vDesk.Visual.Icons.Configuration.Administration,
                        vDesk.Locale.Configuration.AdministrationDescription,
                        () => (new vDesk.Configuration.Remote.ConfigurationWindow()).Show()
                    )
                );
            }

            //Set client-config menu-entry.
            vDesk.Header.MainMenu.Add(
                new vDesk.MainMenu.Item(
                    vDesk.Locale.Configuration.Settings,
                    vDesk.Visual.Icons.Configuration.Settings,
                    vDesk.Locale.Configuration.SettingsDescription,
                    () => (new vDesk.Configuration.Local.ConfigurationWindow()).Show()
                )
            );

            //Set help menu-entry.
            /* Header.MainMenu.Add(
                 new vDesk.MainMenu.Item(
                     vDesk.Locale.vDesk.MaximizeHelp,
                     "Unknown",
                     vDesk.Locale.vDesk.MaximizeHelpDescription,
                     () => (new vDesk.Controls.Dialogs.HelpDialog()).Show()
                 )
             ); */

            //Set about menu-entry.
            vDesk.Header.MainMenu.Add(
                new vDesk.MainMenu.Item(
                    vDesk.Locale.vDesk.About,
                    vDesk.Visual.Icons.Unknown,
                    vDesk.Locale.Configuration.SystemInformations,
                    () => (new vDesk.AboutDialog()).Show()
                )
            );

            //Set logout menu-entry.
            vDesk.Header.MainMenu.Add(
                new vDesk.MainMenu.Item(
                    vDesk.Locale.vDesk.Logout,
                    vDesk.Visual.Icons.Logout,
                    vDesk.Locale.vDesk.LogoutDescription,
                    () => LoginDialog.Logout()
                )
            );

            //Run Modules.
            vDesk.Modules.RunAll();
            vDesk.WorkSpace.Modules = Object.values(vDesk.Modules.Running);

            //Setup clipboard.
            Clipboard = new vDesk.Clipboard();

            LoginDialog.Remove();
        },
        Stop() {
            LoginDialog.Show();
            //Execute unload routines.
            Object.values(this.Unload).forEach(Routine => {
                LoginDialog.Status = Routine.Status;
                Routine.Unload();
            });

            vDesk.Header.Unload();
            vDesk.WorkSpace.Unload();
            vDesk.Footer.Unload();

            LoginDialog.Remove();
        }
    };
})();


/**
 * Represents an enumeration of default values for interfaces.
 * @constant
 * @type Object
 */
const Interface = {
    /**
     * Default value that represents a not implemented member.
     * @type Object
     */
    FieldNotImplemented:  {
        enumerable:   true,
        configurable: true,
        get:          () => {throw new Error("Field not implemented.");}
    },
    /**
     * Default value that represents a not implemented method.
     * @type Function
     */
    MethodNotImplemented: function() {
        throw new Error("Method not implemented.");
    }
};

/**
 * Extension of native objects.
 */
Date.prototype.getISODay = function() {
    switch(this.getDay()) {
        case 1:
            return 0;
        case 2:
            return 1;
        case 3:
            return 2;
        case 4:
            return 3;
        case 5:
            return 4;
        case 6:
            return 5;
        case 0:
            return 6;
    }
};
/**
 * Clones the Date.
 * @return {Date}
 */
Date.prototype.clone = function() {
    return new Date(this.valueOf());
};

if(String.prototype.includes === undefined) {
    String.prototype.includes = function(string) {
        return ~this.indexOf(string);
    };
}
if(String.prototype.startsWith === undefined) {
    String.prototype.startsWith = function(string) {
        return this.indexOf(string) === 0;
    };
}


Object.defineProperties(Function.prototype, {
    Implements: {
        enumerable:   false,
        configurable: false,
        writable:     false,
        /**
         * Applies an interface to a class.
         * @param {Function|Object} Interface The interface to implement.
         */
        value:        function(Interface) {
            switch(typeof Interface) {
                case "function":
                    this.prototype = new Interface();
                    break;
                case "object":
                    this.prototype = Object.create(Interface);
                    break;
            }
            // this.prototype = new Interface();
        }
    }
});

Object.defineProperties(Object.prototype, {
    Extends: {
        enumerable:   false,
        configurable: false,
        writable:     false,
        /**
         * Extends the calling child 'class' with the functionality of a specified
         * parent 'class'.
         * @name Object.prototype.Extends
         * @param {Function} Parent A Type-reference to the parent-class to inherit from.
         * @param {...*} Arguments The arguments to pass to the parent's constructor.
         */
        value:        function(Parent, ...Arguments) {

            //Set the prototype of the extending child constructor once to the parent's constructor to enable instanceof-checks.
            if(!(this.constructor.prototype instanceof Parent)) {
                const Instance = Object.create(Parent.prototype);
                Parent.call(Instance);
                Object.setPrototypeOf(this.constructor.prototype, Instance);
            }

            //Populate an instance of the parent through the 'Parent'-property to the child.
            Object.defineProperty(this, "Parent", {
                value: new Parent(...Arguments)
            });

            //Copy over properties.
            Object.defineProperties(this, Object.keys(this.Parent).reduce((Descriptors, Key) => {
                Descriptors[Key] = Object.getOwnPropertyDescriptor(this.Parent, Key);
                return Descriptors;
            }, {}));
        }
    },
    forEach: {
        enumerable:   false,
        configurable: false,
        writable:     false,
        /**
         * Executes specified predicate on the key-value-pairs of the Object.
         * This method only works on Object-literals.
         * @name Object.prototype.forEach
         * @param {Function} Predicate The predicate to execute.
         */
        value:        function(Predicate) {
            //Return if the method is called from class-scope.
            if(this.constructor.name !== Object.prototype.constructor.name) {
                return;
            }
            for(const KeyValuePair of Object.entries(this)) {
                Predicate(...KeyValuePair.reverse(), this);
            }
        }
    },
    map:     {
        enumerable:   false,
        configurable: false,
        writable:     false,
        /**
         * Executes specified predicate on the key-value-pairs of the Object.
         * This method only works on Object-literals.
         * @name Object.prototype.map
         * @param {Function} Predicate The predicate to execute.
         */
        value:        function(Predicate) {
            //Return if the method is called from class-scope.
            if(this.constructor.name !== Object.prototype.constructor.name) {
                return;
            }
            const Transformed = {};
            for(const KeyValuePair of Object.entries(this)) {
                Transformed[KeyValuePair[0]] = Predicate(...KeyValuePair.reverse(), this);
            }
            return Transformed;
        }
    },
    filter:  {
        enumerable:   false,
        configurable: false,
        writable:     false,
        /**
         * Returns a new Object containing all elements that satisfy a test provided by the specified predicate function.
         * This method only works on Object-literals.
         * @name Object.prototype.forEach
         * @param {Function} Predicate The predicate function to execute on each element inside the Object.
         * @return {Object}  The elements inside the Object which are matching the searchcriteria.
         */
        value:        function(Predicate) {
            //Return if the method is called from class-scope.
            if(this.constructor.name !== Object.prototype.constructor.name) {
                return;
            }
            const Filtered = {};
            for(const KeyValuePair of Object.entries(this)) {
                if(Predicate(...KeyValuePair.reverse(), this)) {
                    Filtered[KeyValuePair[0]] = KeyValuePair[1];
                }
            }
            return Filtered;
        }
    },
    reduce:  {
        enumerable:   false,
        configurable: false,
        writable:     false,
        /**
         * Reduces the values of the Object to a single value.
         * This method only works on Object-literals.
         * @name Object.prototype.forEach
         * @param {Function} Predicate The callback function to apply on each element inside the Object.
         * @param {*} [Initialvalue] Value to use as the first argument to the first call of the Predicate. If no initial value is supplied, the first element in the Object will be used.
         * @return {*} The value that results from the reduction.
         */
        value:        function(Predicate, Initialvalue) {
            let Accumulator = Initialvalue ?? Object.values(this)[0];
            for(const KeyValuePair of Object.entries(this)) {
                Accumulator = Predicate(Accumulator, ...KeyValuePair.reverse(), this);
            }
            return Accumulator;
        }
    }
});

class ArgumentError extends Error {
    constructor(message) {
        super(message);
        this.name = "ArgumentError";
    }
}

class NullReferenceError extends Error {
    constructor(message) {
        super(message);
        this.name = "NullReferenceError";
    }
}

/**
 * Utility class for validating properties and parameters.
 * @type {{Parameter: Ensure.Parameter, Property: Ensure.Property}}
 */
const Ensure = {

    /**
     * Ensures that a passed value of a specified parameter matches one or more specified type(s).
     * @param Value The value to ensure the specified type of.
     * @param {String|Function|StringConstructor|BooleanConstructor|NumberConstructor|ArrayConstructor|Array<String|Function|StringConstructor|BooleanConstructor|NumberConstructor|DateConstructor|ArrayConstructor|DataViewConstructor|ArrayBufferConstructor>} Type The type(s) to ensure.
     * @param {String} Name The name of the parameter.
     * @param {Boolean} Nullable Flag indicating whether the parameter is nullable.
     * @param {?Function} [Throw=null] Predicate for throwing any occurred Error from the lexical scope. (Currently just supported by Google Chrome).
     * @return {Boolean} True if the specified value is of the specified type; otherwise, false.
     */
    Parameter: function(Value, Type, Name, Nullable = false, Throw = null) {

        let Error = null;

        //Check if null has been passed.
        if(Value === null) {
            //Check if the parameter is nullable.
            if(Nullable) {
                return true;
            }
            Error = new NullReferenceError(`Value passed to parameter '${Name}' cannot be null.`);
        }
        //Check if a function (class) has been passed.
        else if(typeof Type === "function" && !(Value instanceof Type)) {
            Error = new TypeError(
                `Value passed to parameter '${Name}' must be an instance of ${Type?.constructor?.name ?? Type.name}, ${Value?.constructor?.name ?? typeof Value} given.`);
        }
        //Check if a scalar type has been passed.
        else if(typeof Type === "string" && typeof Value !== Type) {
            Error = new TypeError(
                `Value passed to parameter '${Name}' must be of the type ${Type}, ${Value?.constructor?.name ?? typeof Value} given.`);
        }
        //Check if an union type has been passed.
        else if(
            Type instanceof Array
            && !Type.some(Type => typeof Type === "function" && Value instanceof Type || typeof Type === "string" && typeof Value === Type)
        ) {
            Error = new TypeError(`Value passed to parameter '${Name}' must be one of the type ${Type.map(Type => Type?.name ?? Type).join(
                "|")}, ${Value?.constructor?.name ?? typeof Value} given.`);
        }

        //Check if an Error has occurred.
        if(Error !== null) {
            if(typeof Throw === "function") {
                Throw(Error);
            } else {
                throw Error;
            }
            return false;
        }
        return true;

    },

    /**
     * Ensures that a value set to a specified property matches one or more specified type(s).
     * @param Value The value to ensure the specified type of.
     * @param {String|Function|StringConstructor|BooleanConstructor|NumberConstructor|DateConstructor|ArrayConstructor|Array<String|Function|StringConstructor|BooleanConstructor|NumberConstructor|DateConstructor|ArrayConstructor>} Type The type(s) to ensure.
     * @param {String} Name The name of the property.
     * @param {Boolean} Nullable Flag indicating whether the property is nullable.
     * @param {?Function} [Throw=null] Predicate for throwing any occurred Error from the lexical scope. (Currently just supported by Google Chrome).
     * @return {Boolean} True if the specified value is of the specified type; otherwise, false.
     */
    Property: function(Value, Type, Name, Nullable = false, Throw = null) {

        let Error = null;

        //Check if null has been set.
        if(Value === null) {
            //Check if the property is nullable.
            if(Nullable) {
                return true;
            }
            Error = new NullReferenceError(`Value set to property '${Name}' cannot be null.`);
        }
        //Check if a function (class) has been passed.
        else if(typeof Type === "function" && !(Value instanceof Type)) {
            Error = new TypeError(
                `Value set to property '${Name}' must be an instance of ${Type?.constructor?.name ?? Type.name}, ${Value?.constructor?.name ?? typeof Value} given.`);
        }
        //Check if a scalar type has been passed.
        else if(typeof Type === "string" && typeof Value !== Type) {
            Error = new TypeError(
                `Value set to property '${Name}' must be of the type ${Type}, ${Value?.constructor?.name ?? typeof Value} given.`);
        }
        //Check if an union type has been passed.
        else if(
            Type instanceof Array
            && !Type.some(Type => typeof Type === "function" && Value instanceof Type || typeof Type === "string" && typeof Value === Type)
        ) {
            Error = new TypeError(`Value set to property '${Name}' must be one of the type ${Type.map(Type => Type?.name ?? Type).join(
                "|")}, ${Value?.constructor?.name ?? typeof Value} given.`);
        }

        //Check if an Error has occurred.
        if(Error !== null) {
            if(typeof Throw === "function") {
                Throw(Error);
            } else {
                throw Error;
            }
            return false;
        }
        return true;
    }
};

/**
 * Holds a reference to any value stored during a drag operation.
 * @type {*}
 */
let ref = undefined;

/**
 * Sets a reference to any value for transmissions during a drag operation.
 * Note: This operation is stateful. So setting multiple values will result in the retrieval of the last set value.
 * @param {*} reference A reference to the value to transmit.
 */
DataTransfer.prototype.setReference = function(reference) {
    this.setData("text/plain", typeof reference);
    ref = reference;
};

/**
 * Retrieves a previously stored reference.
 * @return {*} The reference stored during a drag operation.
 */
DataTransfer.prototype.getReference = function() {
    const reference = ref;
    ref = undefined;
    return reference;
};