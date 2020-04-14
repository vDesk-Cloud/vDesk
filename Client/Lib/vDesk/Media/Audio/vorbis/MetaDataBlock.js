/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


vDesk.Media.Audio.Vorbis.MetadataBlock = function (Type, Length, LastBlock) {

    /**
     * The type of the metadatablock.
     * @type {Number}
     * @ignore
     */
    let _iType = null;
    /**
     * The length in bytes of the metadatablock.
     * @type {Number}
     * @ignore
     */
    let _iLength = null;
    /**
     * Flag indicating whether this block is the last within the blah?
     * @type {Boolean}
     * @ignore
     */
    let _bLastBlock = null;

    Object.defineProperties(this, {
        Type: {
            get: function () {
                return _iType;
            },
            set: function (value) {
                _iType = value;
            }
        },
        Length: {
            get: function () {
                return _iLength;
            },
            set: function (value) {
                _iLength = value;
            }
        },
        LastBlock: {
            get: function () {
                return _bLastBlock;
            },
            set: function (value) {
                _bLastBlock = value;
            }
        }
    });
};