"use strict";
/**
 * Initializes a new instance of the TextFrame class.
 * @class Represents a text-based dataframe of ID3v2.x-Tags.
 * @see http://id3.org/id3v2.3.0#Text_information_frames
 * @param {String} [ID=""] The ID of the TextFrame.
 * @param {Number} [StatusFlags=0] The status flags of the TextFrame.
 * @param {Number} [EncodingFlags=0] The encoding flags of the TextFrame.
 * @param {Uint8Array} Data The data of the TextFrame.
 * @property {String} ID Gets or sets the ID of the TextFrame.
 * @property {Number} StatusFlags Gets or sets the status flags of the TextFrame.
 * @property {Number} EncodingFlags Gets or sets the encoding flags of the TextFrame.
 * @property {String} Data Gets the data of the TextFrame.
 * @property {String} Encoding Gets the encoding of the data of the TextFrame.
 * @memberOf vDesk.Media.Audio.ID3.V2
 * @augments vDesk.Media.Audio.ID3.V2.Frame
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.V2.TextFrame = function(ID, StatusFlags, EncodingFlags, Data) {
    this.Extends(vDesk.Media.Audio.ID3.V2.Frame, ID, StatusFlags, EncodingFlags);

    Object.defineProperties(this, {
        Data:     {
            get:        () => Decoder.decode(Data.slice(1)),
            enumerable: true
        },
        Encoding: {
            get:        () => Encoding,
            enumerable: true
        }
    });

    /**
     * The encoding of the text content of the TextFrame.
     * @type {String}
     */
    const Encoding = Data[0] === 0x00
                     ? vDesk.Media.Audio.ID3.V2.Frame.Encoding.LATIN1
                     : vDesk.Media.Audio.ID3.V2.Frame.Encoding.UTF16;

    /**
     * The TextDecoder of the TextFrame.
     * @type {TextDecoder}
     */
    const Decoder = new TextDecoder(Encoding);
};