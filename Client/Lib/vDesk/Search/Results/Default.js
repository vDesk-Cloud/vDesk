"use strict";
/**
 * @typedef {Object} SearchResult Represents the format of a search-result returned from the server.
 * @property {String} Name The display-name of the search-result.
 * @property {String} Type The type-indentifier of the searchresult.
 * @property {*} Data The data of the searchresult.
 */
/**
 * Initializes a new instance of the Default class.
 * @class Represents a generic, default searchresult for unknown types of search-result.
 * @param {SearchResult} Result The data of the returned search-result.
 * @implements {vDesk.Search.IResult}
 * @memberOf vDesk.Search.Results
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Search.Results.Default = function Default(Result) {

    Object.defineProperty(this, "Icon", {
        enumerable: true,
        value: "help"
    });

    Object.defineProperty(this, "Name", {
        enumerable: true,
        get: function () {
            return Result.Name;
        }
    });

};
vDesk.Search.Results.Default.Implements(vDesk.Search.IResult);