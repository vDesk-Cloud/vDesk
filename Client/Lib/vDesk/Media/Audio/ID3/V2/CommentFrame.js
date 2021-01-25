"use strict";
/**
 * Initializes a new instance of the CommentFrame class.
 * @class Represents a text-based commentary dataframe of ID3v2.x-Tags.
 * @see http://id3.org/id3v2.3.0#Comments
 * @param {String} [ID=""] The ID of the CommentFrame.
 * @param {Number} [StatusFlags=0] The status flags of the CommentFrame.
 * @param {Number} [EncodingFlags=0] The encoding flags of the CommentFrame.
 * @param {Uint8Array} Data The data of the CommentFrame.
 * @property {String} ID Gets or sets the ID of the CommentFrame.
 * @property {Number} StatusFlags Gets or sets the status flags of the CommentFrame.
 * @property {Number} EncodingFlags Gets or sets the encoding flags of the CommentFrame.
 * @property {String} Data Gets the data of the CommentFrame.
 * @property {String} Encoding Gets the encoding of the data of the CommentFrame.
 * @property {String} Language Gets the language of the data of the CommentFrame.
 * @property {String} ShortDescription Gets the short description of the CommentFrame.
 * @memberOf vDesk.Media.Audio.ID3.V2
 * @augments vDesk.Media.Audio.ID3.V2.Frame
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.V2.CommentFrame = function CommentFrame(ID, StatusFlags, EncodingFlags, Data) {
    this.Extends(vDesk.Media.Audio.ID3.V2.Frame, ID, StatusFlags, EncodingFlags);
    Object.defineProperties(this, {
        Data:             {
            get:        () => Decoder.decode(Data.slice(DescriptionIndex + 1)),
            enumerable: true
        },
        Encoding:         {
            get:        () => Encoding,
            enumerable: true
        },
        Language:         {
            get:        () => Language,
            enumerable: true
        },
        ShortDescription: {
            get:        () => ShortDescription,
            enumerable: true
        }
    });

    /**
     * The encoding of the text content of the CommentFrame.
     * @type {String}
     */
    const Encoding = (Data[0] === 0x00)
                     ? vDesk.Media.Audio.ID3.V2.Frame.Encoding.LATIN1
                     : vDesk.Media.Audio.ID3.V2.Frame.Encoding.UTF16;

    /**
     * The TextDecoder of the CommentFrame.
     * @type {TextDecoder}
     */
    const Decoder = new TextDecoder(Encoding);

    /**
     * The language of the CommentFrame.
     * @type {String}
     */
    const Language = String.fromCharCode(Data[1]) + String.fromCharCode(Data[2]) + String.fromCharCode(Data[3]);

    /**
     * The index of the description of the CommentFrame.
     * @type {Number}
     */
    let DescriptionIndex = Data.indexOf(0x00, 4);

    /**
     * The short description of the CommentFrame.
     * @type {String}
     */
    const ShortDescription = Decoder.decode(Data.slice(4, DescriptionIndex));

    //Check if the description is a 'null-terminated null-string'.
    if(ShortDescription === "" && Data[DescriptionIndex + 1] === 0x00) {
        ++DescriptionIndex;
    }
};