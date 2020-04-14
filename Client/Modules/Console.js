"use strict";
/**
 * Console Module.
 * @param {vDesk.Security.User} [User=vDesk.User] Initializes the Console with the specified User.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Name Gets the name of the Console.
 * @property {vDesk.Security.User} User Gets or sets the current User of the Console.
 * @memberOf Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
Modules.Console = function Console(User = vDesk.User) {

    this.Extends(vDesk.Controls.Window);

    /**
     * The command history of the Console.
     * @type {Array<String>}
     */
    let History = [];

    /**
     * The current index of the command history of the Console.
     * @type {number}
     */
    let Index = 0;

    /**
     * The current User of the Console.
     * @type {Object|vDesk.Security.User}
     */

    Object.defineProperties(this, {
        Name: {
            enumerable: true,
            value:      "Console"
        },
        User: {
            enumerable: true,
            get:        () => User,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "User");
                User = Value;
                this.Title = `Console ${Value.Name}@vDesk`;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'keydown' event.
     * @param {KeyboardEvent} Event
     */
    const OnKeyDown = Event => {
        switch(Event.key) {
            case "Tab":
                InputText.textContent = Object.keys(vDesk.Console.Commands).find(Command => Command.startsWith(InputText.textContent)) || InputText.textContent;
                Event.preventDefault();
                break;
            case "ArrowUp":
                Event.preventDefault();
                if(InputText.textContent.length === 0) {
                    InputText.textContent = History[Index];
                } else if(Index > 0) {
                    InputText.textContent = History[--Index];
                }
                break;
            case "ArrowDown":
                Event.preventDefault();
                if(Index < History.length - 1) {
                    InputText.textContent = History[++Index];
                } else {
                    InputText.textContent = "";
                }
                break;
            default:
                return;
        }
        ResetCaret();
    };

    /**
     * Eventhandler that listens on the 'keypress' event.
     * @param {KeyboardEvent} Event
     */
    const OnKeyPressCircumflex = Event => {
        if(Event.key === "°") {
            this.Toggle();
        }
    };

    /**
     * Resets the caret of the input text to the last position.
     */
    const ResetCaret = function() {
        const Range = document.createRange();
        Range.selectNodeContents(InputText);
        Range.collapse(false);
        const Selection = window.getSelection();
        Selection.removeAllRanges();
        Selection.addRange(Range);
    };

    /**
     * Toggles the visibility of the Console.
     * @param {Boolean|null} [Visible=null] Theo optional visibility to force.
     */
    this.Toggle = async function(Visible = null) {
        Ensure.Parameter(Visible, Type.Boolean, "Visible", true);

        if(this.Visible) {
            History = [];
            InputText.blur();
            this.Close();
        } else {
            this.Show();
            InputText.focus();
        }
        User = vDesk.User;

        //Infinitely read the Console while it's visible.
        while(this.Visible) {
            InputMessage.textContent = "";
            const Line = await this.Read(`${User.Name}>`);
            //@todo Line.split("|") and pipe output of previous command to next command;
            const Command = Object.keys(vDesk.Console.Commands).find(Command => Line.startsWith(Command));
            if(Command === undefined) {
                this.Write(`Command "${Line}" not found!`);
            } else {
                await vDesk.Console.Commands[Command](
                    this,
                    //Console argument parser in a nutshell; lel.
                    Line.replace(Command)
                        .trim()
                        .split("-")
                        .filter(Parameter => Parameter.length > 1)
                        .map(Parameter => Parameter.trim().split("="))
                        .reduce(
                            (Parameters, Parameter) => {
                                //Check if the parameter is a JSON string.
                                if(
                                    Parameter[1].startsWith("{") && Parameter[1].endsWith("}")
                                    || Parameter[1].startsWith("[") && Parameter[1].endsWith("]")
                                ) {
                                    Parameters[Parameter[0]] = JSON.parse(Parameter[1]);
                                    return Parameters;
                                }
                                //Check if an array of values has been passed.
                                const Values = Parameter[1].split(", ");
                                if(Values.length > 1) {
                                    Parameters[Parameter[0]] = Values.map(Value => !/^[a-zA-Z]/.test(Value) ? JSON.parse(Value) : Value);
                                    return Parameters;
                                }
                                Parameters[Parameter[0]] = !/^[a-zA-Z]/.test(Parameter[1]) ? JSON.parse(Parameter[1]) : Parameter[1];
                                return Parameters;
                            }
                        )
                );
                if(Line !== History[History.length - 1]) {
                    History.push(Line);
                    Index = History.length - 1;
                    if(History.length > 20) {
                        History.shift();
                    }
                }
                Control.scrollTop = Control.offsetHeight;
            }
        }
    };

    /**
     * Reads the input from the Console.
     * @param {String} [Message=""] The message to display while reading the input from the Console.
     * @return {Promise<String>} A Promise that resolves to the read input of the Console.
     */
    this.Read = async function(Message = "") {
        Ensure.Parameter(Message, Type.String, "Message");
        InputMessage.textContent += Message;
        return new Promise(Resolve => {
            const OnKeyPress = Event => {
                switch(Event.key) {
                    case "°":
                        //Cancel reading if the Console is being closed.
                        window.removeEventListener("keypress", OnKeyPress);
                        break;
                    case "Enter":
                        const Input = document.createElement("li");
                        Input.className = "Input";
                        Input.appendChild(InputMessage.cloneNode(true));
                        const Text = document.createElement("span");
                        Text.className = "Text";
                        Text.textContent = InputText.textContent;
                        Input.appendChild(Text);
                        IO.appendChild(Input);
                        Resolve(InputText.textContent);
                        InputText.textContent = "";
                        window.removeEventListener("keypress", OnKeyPress);
                        break;
                    default:
                        return;
                }

            };
            window.addEventListener("keypress", OnKeyPress);
        });
    };

    /**
     * Writes a line to the Console.
     * @param {String} Line The line to write.
     */
    this.Write = function(Line) {
        Ensure.Parameter(Line, Type.String, "Line");
        const Output = document.createElement("li");
        Output.className = "Output";
        // Output.appendChild(InputMessage.cloneNode(true));
        const Text = document.createElement("span");
        Text.className = "Text";
        Text.textContent = Line;
        Output.appendChild(Text);
        IO.appendChild(Output);
    };

    /**
     * Clears the Console.
     */
    this.Clear = function() {
        while(IO.hasChildNodes()) {
            IO.removeChild(IO.lastChild);
        }
    };

    /**
     * Loads the Console.
     */
    this.Load = function() {
        window.addEventListener("keypress", OnKeyPressCircumflex);
    };

    /**
     * Unloads the Console.
     */
    this.Unload = function() {
        window.removeEventListener("keypress", OnKeyPressCircumflex);
        this.Close();
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.addEventListener("click", () => InputText.focus());
    Control.className = "Console";

    /**
     * The Input/OutputList of the Console.
     * @type {HTMLUListElement}
     */
    const IO = document.createElement("ul");
    IO.className = "IO";
    Control.appendChild(IO);

    /**
     * The input of the Console.
     * @type {HTMLDivElement}
     */
    const Input = document.createElement("div");
    Input.className = "Input";
    Control.appendChild(Input);

    /**
     * The input message of the Console.
     * @type {HTMLSpanElement}
     */
    const InputMessage = document.createElement("span");
    InputMessage.className = "Message";
    Input.appendChild(InputMessage);

    /**
     * The input TextBox of the Console.
     * @type {HTMLSpanElement}
     */
    const InputText = document.createElement("span");
    InputText.contentEditable = true.toString();
    InputText.className = "Text";
    InputText.addEventListener("focus", () => window.addEventListener("keydown", OnKeyDown, {capture: true}));
    InputText.addEventListener("blur", () => window.removeEventListener("keydown", OnKeyDown, {capture: true}));
    Input.appendChild(InputText);

    window.addEventListener("keypress", OnKeyPressCircumflex);
    this.Title = `Console ${User.Name}@vDesk`;
    this.Content.appendChild(Control);

    this.Icon = vDesk.Visual.Icons.Console.Module;

};

Modules.Console.Implements(vDesk.Modules.IModule);