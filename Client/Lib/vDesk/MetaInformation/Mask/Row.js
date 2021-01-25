"use strict";

/**
 * Initializes a new instance of the Row class.
 * @class Represents a row defining the name, order and type, of a mask.
 * @param {?Number} [ID=null] The ID of the Row.
 * @param {Number} [Index=0] The index of the Row.
 * @param {String} [Name=""] The name of the Row.
 * @param {String} [Type=vDesk.Struct.Type.String] The type of the Row.
 * @param {Boolean} [Required=false] Flag indicating whether each associated {@link }vDesk.MetaInformation.DataSet.Row|Row} requires a value.
 * @param {Object} [Validator=null] Initializes the Row with the specified validator.
 * @property {?Number} ID Gets or sets the ID of the Row.
 * @property {Number} Index Gets or sets the index of the Row.
 * @property {String} Name Gets or sets the name of the Row.
 * @property {String} Type Gets or sets the content type of the Row.
 * @property {Boolean} Required Gets or sets a value indicating whether each associated {@link vDesk.MetaInformation.DataSet.Row|Row} requires a value.
 * @property {Object} Validator Gets or sets the validator of the Row.
 * @memberOf vDesk.MetaInformation.Mask
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.Mask.Row = function Row(
    ID        = null,
    Index     = 0,
    Name      = "",
    Type      = vDesk.Struct.Type.String,
    Required  = false,
    Validator = null
) {
    Ensure.Parameter(ID, vDesk.Struct.Type.Number, "ID", true);
    Ensure.Parameter(Index, vDesk.Struct.Type.Number, "Index");
    Ensure.Parameter(Name, vDesk.Struct.Type.String, "Name");
    Ensure.Parameter(Type, vDesk.Struct.Type.String, "Type");
    Ensure.Parameter(Required, vDesk.Struct.Type.Boolean, "Required");
    Ensure.Parameter(Validator, vDesk.Struct.Type.Object, "Validator", true);

    Object.defineProperties(this, {
        ID:        {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "ID", true);
                ID = Value;
            }
        },
        Index:     {
            enumerable: true,
            get:        () => Index,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "Index");
                Index = Value;
            }
        },
        Name:      {
            enumerable: true,
            get:        () => Name,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Name");
                Name = Value;
            }
        },
        Type:      {
            enumerable: true,
            get:        () => Type,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Type");
                Type = Value;
            }
        },
        Required:  {
            enumerable: true,
            get:        () => Required,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Required");
                Required = Value;
            }
        },
        Validator: {
            enumerable: true,
            get:        () => Validator,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Object, "Validator", true);
                Validator = Value;
            }
        }
    });

};

/**
 * Factory method that creates a Row from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Row.
 * @return {vDesk.MetaInformation.Mask.Row} A Row filled with the provided data.
 */
vDesk.MetaInformation.Mask.Row.FromDataView = function(DataView) {
    Ensure.Parameter(DataView, Type.Object, "DataView");
    return new vDesk.MetaInformation.Mask.Row(
        DataView?.ID ?? null,
        DataView?.Index ?? 0,
        DataView?.Name ?? "",
        DataView?.Type ?? vDesk.Struct.Type.String,
        DataView?.Required ?? false,
        DataView?.Validator ?? null
    );
};

vDesk.MetaInformation.Mask.Row.Options = {};