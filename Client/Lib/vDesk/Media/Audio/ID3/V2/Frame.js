"use strict";
/**
 * Initializes a new instance of the Frame class.
 * @class Represents a baseclass for dataframes of ID3v2.x-Tags.
 * @see http://id3.org/id3v2.3.0#ID3v2_frame_overview
 * @param {String} [ID=""] The ID of the frame.
 * @param {Number} [StatusFlags=0] The status flags of the frame.
 * @param {Number} [EncodingFlags=0] The encoding flags of the frame.
 * @property {String} ID Gets or sets the ID of the frame.
 * @property {Number} StatusFlags Gets or sets the status flags of the frame. @see http://id3.org/id3v2.3.0#Frame_header_flags
 * @property {Number} EncodingFlags Gets or sets the encoding flags of the frame. @see http://id3.org/id3v2.3.0#Frame_header_flags
 * @property {Boolean} TagAlterPreservation Gets a value indicating whether the frame should be preserved if the ID3tag is being altered.
 * @property {Boolean} FileAlterPreservation Gets a value indicating whether the frame should be preserved if the file which contains the ID3tag is being altered.
 * @property {Boolean} ReadOnly Gets a value indicating whether the contents of the frame is intended to be read only.
 * @property {Boolean} ReadOnly Gets a value indicating whether the frame is compressed.
 * @property {Boolean} Encryption Gets a value indicating whether the frame is encrypted.
 * @property {Boolean} GroupingIdentity Gets a value indicating whether the frame belongs in a group with other frames.
 * @implements vDesk.Media.Audio.ID3.IFrame
 * @memberOf vDesk.Media.Audio.ID3.V2
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.V2.Frame = function Frame(ID = "", StatusFlags = 0x00, EncodingFlags = 0x00) {
    Ensure.Parameter(ID, Type.String, "ID");
    Ensure.Parameter(StatusFlags, Type.Number, "StatusFlags");
    Ensure.Parameter(EncodingFlags, Type.Number, "EncodingFlags");

    Object.defineProperties(this, {
        ID:                    {
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.String, "ID");
                ID = Value;
            },
            enumerable: true
        },
        StatusFlags:           {
            get:        () => StatusFlags,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID");
                StatusFlags = Value;
            },
            enumerable: true
        },
        EncodingFlags:         {
            get:        () => EncodingFlags,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "EncodingFlags");
                EncodingFlags = Value;
            },
            enumerable: true
        },
        TagAlterPreservation:  {
            get:        () => StatusFlags & 1,
            enumerable: true
        },
        FileAlterPreservation: {
            get:        () => StatusFlags & 2,
            enumerable: true
        },
        ReadOnly:              {
            get:        () => StatusFlags & 4,
            enumerable: true
        },
        Compression:           {
            get:        () => EncodingFlags & 1,
            enumerable: true
        },
        Encryption:            {
            get:        () => EncodingFlags & 2,
            enumerable: true
        },
        GroupingIdentity:      {
            get:        () => EncodingFlags & 4,
            enumerable: true
        }
    });
};
/**
 * Enumeration of possible encodings of textual-content of ID3v2.x-frames.
 * @readonly
 * @enum {String}
 */
vDesk.Media.Audio.ID3.V2.Frame.Encoding = {
    /**
     * UTF-8 encoding.
     * @type String
     */
    UTF8:   "utf-8",
    /**
     * UTF-16 encoding.
     * @type String
     */
    UTF16:  "utf-16",
    /**
     * Latin-1/ISO 8859-1 encoding.
     * @type String
     */
    LATIN1: "windows-1252"
};

vDesk.Media.Audio.ID3.V2.Frame.Implements(vDesk.Media.Audio.ID3.IFrame);