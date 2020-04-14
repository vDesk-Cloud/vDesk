"use strict";
/**
 * The response object returned from the server after executing a Command.
 * @typedef {Object} Response The response of the last executed Command.
 * @property {String} Module Gets the name of the module which has been called by the last executed Command.
 * @property {String} Command Gets the name of the last executed Command.
 * @property {Boolean} Status Gets a value that indicates whether the Command has been successfully executed.
 * @property {*} Data Gets any data that has been returned from the server.
 */
/**
 * Fired if the ticket of the current User has expired.
 * @event vDesk.Connection#ticketexpired
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'expired' event.
 */
/**
 * Initializes a new instance of the Connection class.
 * @class Represents a singleton gateway to a vDesk server.
 * @hideconstructor
 * @property {String} Address Gets the address of the server.
 * @memberOf vDesk.Connection
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Connection = (function Connection() {

    /**
     * The address to the server.
     * @type String
     */
    let Address = null;

    /**
     * The LoadingCircle, providing visual feedback while transferring data.
     * @type null|vDesk.Controls.LoadingCircle
     */
    let Circle = null;

    /**
     * Converts datetimestrings into Date objects.
     * @param {String} Key
     * @param {String} Value
     * @return {Date|Object}
     */
    const ConvertDateString = function(Key, Value) {
        return typeof Value === Type.String && vDesk.Utils.Expression.DateTimeUTCGMT.test(Value) ? new Date(Value) : Value;
    };

    /**
     * Connects to a server and performs a handshake.
     * @name vDesk.Connection#Connect
     * @param {String} TargetAddress The address of the target server to connect to.
     * @return {Boolean} True if the specified address could be reached and the current client is compatible to the remote server.
     */
    const Connect = function(TargetAddress) {
        Ensure.Parameter(TargetAddress, Type.String, "Address", true);
        Circle = new vDesk.Controls.LoadingCircle();

        //Check if the given address matches a required pattern.
        if(
            !vDesk.Utils.Expression.URI.test(TargetAddress)
            && !vDesk.Utils.Expression.IPv4.test(TargetAddress)
            && !vDesk.Utils.Expression.IPv6.test(TargetAddress)
            && !TargetAddress.includes("localhost")
        ) {
            throw new ArgumentError(`'${TargetAddress}' is not a valid address.`);
        }
        Address = vDesk.Utils.SanitizeURL(TargetAddress);

        return Send(new vDesk.Modules.Command(
            {
                Module:     "Modules",
                Command:    "Connect",
                //@todo Provide the version of every Client-Module in the future, so the server can check the compatibility.
                Parameters: {Version: "0.1.2"}
            }
        )).Data;
    };

    /**
     * Prepares and sends a Command to the server for execution.
     * @name vDesk.Connection#Send
     * @throws SyntaxError Thrown if the Connection has not been connected to a server using 'Connection.Connect(Address)' before.
     * @fires vDesk.Connection#ticketexpired
     * @param {vDesk.Modules.Command} Command The command to execute on the server.
     * @param {?Function} [Response=null] If set, is called when the server has responded. Results in asynchronous execution.
     * @param {Boolean} [Binary=false] Flag indicating whether the result will be returned as blob or a parsed object. Only available in asynchronous mode.
     * @param {?Function} [Progress=null] If set, passes the progress to the callback.
     * @return {Response|undefined} The response from the server, if the command is executed synchronous.
     */
    const Send = function(Command, Response = null, Binary = false, Progress = null) {
        if(Address === null) {
            throw new SyntaxError("The Connection has to connect to a server before sending any Commands.");
        }
        Ensure.Parameter(Command, vDesk.Modules.Command, "Command");
        Ensure.Parameter(Response, Type.Function, "Response", true);
        Ensure.Parameter(Binary, Type.Boolean, "Binary");
        Ensure.Parameter(Progress, Type.Function, "Progress", true);

        const TransmissionProgress = {
            upload:   0,
            download: 0
        };

        //Create and prepare request.
        const Request = new XMLHttpRequest();

        Circle.Show();
        Request.open("POST", Address + Command.Module + Command.Command, Response !== null);
        if(Command.Data instanceof vDesk.Utils.FormData) {
            Request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        }
        Command.Cancel = () => Request.abort();

        //Check if a callback has been passed.
        if(Response !== null) {
            //Add eventlisener for readystatechange.
            Request.addEventListener("readystatechange", () => {
                //Check if server has answered and pass the response to the callback.
                if(Request.readyState === 4 && Request.status === 200) {
                    Circle.Hide();
                    if(Binary) {
                        Response(Request.response);
                    } else {
                        const TransmissionResponse = JSON.parse(Request.responseText, ConvertDateString);
                        if(TransmissionResponse.Code === 1001) {
                            new vDesk.Events.BubblingEvent("ticketexpired").Dispatch(window);
                        }
                        Response(TransmissionResponse);
                    }
                }
            }, false);
            //Check if a callback for capturing the progress has been passed.
            if(Progress !== null) {
                //If true, append it to the progress event.
                Request.upload.addEventListener("progress", Event => {
                    TransmissionProgress.upload = Event;
                    Progress(TransmissionProgress);
                }, false);
                Request.addEventListener("progress", Event => {
                    TransmissionProgress.download = Event;
                    Progress(TransmissionProgress);
                }, false);
            }

            //Set the awaited responsetype.
            Request.responseType = Binary ? "arraybuffer" : "text";
            //Send the command to the server.
            Request.send(Command.Data);

        } else {
            //Else send the command to the server and return the response.
            Request.send(Command.Data);

            if(Request.status === 200) {
                Circle.Hide();
                const Response = JSON.parse(Request.responseText, ConvertDateString);
                if(Response.Data === "ticketexpired") {
                    new vDesk.Events.BubblingEvent("ticketexpired").Dispatch(window);
                }
                return Response;
            }
        }
    };

    /**
     * Prepares and sends a Command to the server for execution and returns the response in a Promise.
     * @name vDesk.Connection#Execute
     * @fires vDesk.Connection#ticketexpired
     * @param {vDesk.Modules.Command} Command The command to execute on the server.
     * @param {Boolean} [Binary=false] Flag indicating whether the result will be returned as blob or a parsed object.
     * @return {Promise<Response>}
     */
    const Execute = async function(Command, Binary = false) {
        Ensure.Parameter(Command, vDesk.Modules.Command, "Command");
        Ensure.Parameter(Binary, Type.Boolean, "Binary");
        Circle.Show();
        return fetch(
            new Request(
                Address + Command.Module + Command.Command,
                {
                    method:  "POST",
                    headers: {
                        "Content-Type": Command.Data instanceof vDesk.Utils.FormData ? "application/x-www-form-urlencoded" : "multipart/form-data"
                    },
                    body:    Command.Data
                }
            )
        )
            .then(Response => {
                Circle.Hide();
                if(Binary) {
                    return Response.blob();
                }
                return Response.text().then(
                    Text => new Promise((Resolve, Reject) => {
                        const Response = JSON.parse(Text, ConvertDateString);
                        if(Response.Status) {
                            Resolve(Response);
                        } else {
                            if(Response.Code === 1001) {
                                new vDesk.Events.BubblingEvent("ticketexpired").Dispatch(window);
                            }
                            Reject(Response);
                        }
                    })
                );
            });
    };

    return {
        Connect: Connect,
        Send:    Send,
        Execute: Execute,
        get Address() {
            return Address;
        }
    };
})();
