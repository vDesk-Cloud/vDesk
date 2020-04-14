/**
 * Namespace that contains validation related functionality.
 * @namespace Validate
 * @memberOf vDesk.Utils
 */
vDesk.Utils.Validate = {
    /**
     * Validates a specified value for a specified type.
     * @name vDesk.Utils.Validate.As
     * @param {*} Value The value to validate.
     * @param {String} Type The type of the value to validate.
     * @return {Boolean} True if the specified value is of the specified type; otherwise, false.
     */
    As: function(Value, Type, Validator = null) {
        switch(Type) {
            case vDesk.Struct.Type.Int:
                return Number.isInteger(Value);
            case vDesk.Struct.Type.Float:
                return Value % 1 !== 0;
            case vDesk.Struct.Type.String:
                return typeof Value === vDesk.Struct.Type.String;
            case vDesk.Struct.Type.Array:
                return Value instanceof Array;
            case vDesk.Struct.Type.Object:
                return Value instanceof Object;
            case vDesk.Struct.Type.Bool:
            case vDesk.Struct.Type.Boolean:
                return typeof Value === vDesk.Struct.Type.Boolean;
            case Extension.Type.Color:
                return Value instanceof vDesk.Media.Drawing.Color
                       || Value.match(vDesk.Utils.Expression.ColorHexString)
                       || Value.match(vDesk.Utils.Expression.ColorRGBString)
                       || Value.match(vDesk.Utils.Expression.ColorRGBAString)
                       || Value.match(vDesk.Utils.Expression.ColorHSLString)
                       || Value.match(vDesk.Utils.Expression.ColorHSLAString);
            case Extension.Type.URL:
                return Value.match(vDesk.Utils.Expression.URL);
            case Extension.Type.Email:
                return Value.match(vDesk.Utils.Expression.Email);
            case Extension.Type.File:
                return Value instanceof File
                       || Value instanceof Blob;
            case Extension.Type.Money:
                return Value.match(vDesk.Utils.Expression.Money);
            case Extension.Type.Date:
            case Extension.Type.Time:
            case Extension.Type.DateTime:
                return Value instanceof Date;
            case vDesk.Struct.Type.Mixed:
                return Value !== undefined;
        }
    }
};