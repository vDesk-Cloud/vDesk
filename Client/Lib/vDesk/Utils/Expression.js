/**
 * Enumeration of general regular expressions.
 * @readonly
 * @enum {RegExp}
 * @memberOf vDesk.Utils
 */
vDesk.Utils.Expression = {
    /**
     * Matches a string against the URL pattern.
     * @type {RegExp}
     */
    URL: /^(https?):\/\//,

    /**
     * Checks if a string is a valid Uniform Resource Identifier.
     * @type {RegExp}
     */
    URI: /(?:([-a-z0-9~!$%^&*_=+}{\'?]+)\.)([-a-z0-9~!$%^&*_=+}{\'?]+)\.(?:([a-z]{2,})(?:\.([a-z0-9]{2,}))*)/,

    /**
     * Checks if a string is a valid IP-adress against the IPv4-specification.
     * @type {RegExp}
     */
    IPv4: /\b(?:(25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9]))\b/,

    /**
     * Checks if a string is a valid IP-adress against the IPv6-specification.
     * @type {RegExp}
     */
    IPv6: /(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/,

    /**
     * Checks if a string is a valid german date.
     * @type {RegExp}
     */
    Date: /^(\d{1,2})\.(\d{1,2})\.(\d{4})$/,

    /**
     * Checks if a string is a valid german time.
     * @type {RegExp}
     */
    Time: /^([0-2][0-3]|[0-1][0-9])\:([0-5][0-9])$/,

    /**
     * Checks if a string is a valid date and time according to UTC Time with +02:00 timezone offset.
     * @type {RegExp}
     */
    DateTimeUTCGMT: /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\+\d{2}:\d{2})$/,

    /**
     * Checks if a string is a valid date and time according to UTC Time with +00:00 timezone offset.
     * @type {RegExp}
     */
    DateTimeUTC: /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d{3})Z$/,

    /**
     * Checks if a string is a valid email-adress according to some random rfc-spec i read a few days ago on wikipedia.
     * @type {RegExp}
     */
    Email: /^([-a-z0-9~!$%^&*_=+}{\'?.]+)@([a-z0-9_][-a-z0-9_]+)\.(?:([a-z]{2,})(?:\.([a-z]{2,}))*)$/,

    /**
     * Checks if a string is a valid price in $ or €, either dot or comma separated or a single number.
     * @type {RegExp}
     */
    Money: /(?:(^\d{1,})|(\d{1,})(?:\.?|\,?)(\d{2}))(€|\$)$/,

    /**
     * Checks if a string is a valid decimal numeric value '1,4'/'1.4'.
     * @type {RegExp}
     */
    Float: /^\d+(?:\.|\,)\d+$/,

    /**
     * Checks if a string is a valid numeric value.
     * @type {RegExp}
     */
    Integer: /^\d+$/,

    /**
     * Checks if a string is a valid timespan in the format of '000:00:00'.
     * @type {RegExp}
     */
    TimeSpan: /^([0-9]{2,3})\:([0-5][0-9])\:([0-5][0-9])$/,

    /**
     * Checks if a string is a valid telephone number.
     * @type {RegExp}
     */
    Telephone: /\+?\d{1,4}?[-.\s]?\(?\d{1,3}?\)?[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}/g,

    /**
     * Checks if a string is a valid RGB-color.
     * @type {RegExp}
     */
    ColorCssString: /(rgb|hsl)a\((\d{1,3}%?,\s?){3}(1|0?\.\d+)\)|(rgb|hsl)\(\d{1,3}%?(,\s?\d{1,3}%?){2}\)/gi,

    /**
     * Checks if a string is a valid hexadecimal color string.
     * @type {RegExp}
     */
    ColorHexString: /#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i,

    /**
     * Checks if a string is a valid RGB color string.
     * @type {RegExp}
     */
    ColorRGBString: /^rgb\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\)$/i,

    /**
     * Checks if a string is a valid RGBA color string.
     * @type {RegExp}
     */
    ColorRGBAString: /^rgba\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\,\s?(1|0?\.\d+)\)$/i,

    /**
     * Checks if a string is a valid HSL color string.
     * @type {RegExp}
     */
    ColorHSLString: /^hsl\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\)$/i,

    /**
     * Checks if a string is a valid HSLA color string.
     * @type {RegExp}
     */
    ColorHSLAString: /^hsla\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\,\s?(1|0?\.\d+)\)$/i
};