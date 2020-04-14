/**
 * Contains utility-functions.
 * @namespace Utils
 * @memberOf vDesk
 */
vDesk.Utils = {
    /**
     * Sanitizes an URL or IP address.
     * @param {String} URL The URL or IP address to sanitize.
     * @return {String} The sanitized URL or IP address.
     */
    SanitizeURL: function(URL) {
        if(!this.Expression.URL.test(URL)) {
            URL = String("http://").concat(URL);
        }
        if(URL.substr(URL.length - 1, 1).match("/")) {
            return URL.concat("vDesk.php?");
        }
        if(!~URL.lastIndexOf("/vDesk.php?")) {
            return URL.concat("/vDesk.php?");
        }
        return URL;
    }
};