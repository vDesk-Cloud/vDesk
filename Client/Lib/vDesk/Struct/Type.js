"use strict";
const Type = {

    /**
     * string
     */
    String: "string",

    /**
     * number
     */
    Number: "number",

    /**
     * int
     */
    Int: "int",

    /**
     * float
     */
    Float: "float",

    /**
     * bool
     */
    Bool: "bool",

    /**
     * boolean
     */
    Boolean: "boolean",

    /**
     * mixed PHP-type / any / *
     */
    Mixed: "mixed",

    /**
     * array
     */
    Array: "array",

    /**
     * object
     */
    Object: "object",

    /**
     * function
     */
    Callable: "function",

    /**
     * function
     */
    Function: "function",

    Scalar: [
        this.String,
        this.Number,
        this.Int,
        this.Float,
        this.Bool
    ],

    /**
     * Determines the type of a specified value.
     *
     * @param {*} Value The value to determine its type of.
     *
     * @return {String} The name of the type of the specified value.
     */
    Of(Value) {
        return Value?.constructor?.name ?? typeof Value;
    },
    /**
     * Determines whether a specified value is of a scalar type.
     * @param {*} Value The value to check.
     * @return {Boolean} True if the specified value is of a scalar type; otherwise, false.
     */
    IsScalar(Value) {
        switch(typeof Value) {
            case this.Number:
            case this.String:
            case this.Bool:
            case this.Boolean:
                return true;
            default:
                return false;
        }
    }
};

/**
 * @memberOf vDesk.Struct
 */
vDesk.Struct.Type = Type;