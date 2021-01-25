"use strict";
/**
 * Initializes a new instance of the URLFrame class.
 * @class Represents a text-based dataframe containing URLs of ID3v2.x-Tags.
 * @see http://id3.org/id3v2.3.0#URL_link_frames
 * @param {String} [ID=""] The ID of the URLFrame.
 * @param {Number} [StatusFlags=0] The status flags of the URLFrame.
 * @param {Number} [EncodingFlags=0] The encoding flags of the URLFrame.
 * @param {Uint8Array} Data The data of the URLFrame.
 * @property {String} ID Gets or sets the ID of the URLFrame.
 * @property {Number} StatusFlags Gets or sets the status flags of the URLFrame.
 * @property {Number} EncodingFlags Gets or sets the encoding flags of the URLFrame.
 * @property {String} Data Gets the data of the URLFrame.
 * @memberOf vDesk.Media.Audio.ID3.V2
 * @augments vDesk.Media.Audio.ID3.V2.Frame
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.V2.URLFrame = function URLFrame(ID, StatusFlags, EncodingFlags, Data) {
    this.Extends(vDesk.Media.Audio.ID3.V2.Frame, ID, StatusFlags, EncodingFlags);

    Object.defineProperties(this, {
        Data: {
            get:        () => Decoder.decode(Data.slice(1, Data.indexOf(0x00))),
            enumerable: true
        }
    });

    /**
     * The TextDecoder of the TextFrame.
     * @type {TextDecoder}
     */
    const Decoder = new TextDecoder(vDesk.Media.Audio.ID3.V2.Frame.Encoding.LATIN1);

};