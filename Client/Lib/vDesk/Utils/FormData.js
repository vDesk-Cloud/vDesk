/**
 * Initializes a new instance of the FormData class.
 * @class Represents a set of key/value pairs representing form fields and their values.
 * The constructed data is represented in an url-encoded syntax, acting as a lighter version as the data created by the native FormData-object.
 * @memberOf vDesk.Utils
 * @augments FormData
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Utils.FormData = function() {
    /**
     * The values of the formdata.
     * @type Array<String>
     */
    const Values = [];

    /**
     * Appends a new value to the FormData.
     * @param {String} Key The key of the data to add.
     * @param {String} Value The data to add.
     */
    this.append = function(Key, Value) {
        Values.push(`${Key}=${Value}`);
    };
    /**
     * Called within XMLHttpRequest.send();
     * @ignore
     */
    this.toString = function() {
        return Values.join("&");
    };
};
vDesk.Utils.FormData.Implements(FormData);
