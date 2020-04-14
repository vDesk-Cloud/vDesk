/**
 * Interface for classes that represent a data frame of an ID3Tag.
 * @interface
 * @memberOf vDesk.Media.Audio.ID3
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.IFrame = function() {};
vDesk.Media.Audio.ID3.IFrame.prototype = {

    /**
     * Gets the ID of the IFrame.
     * @abstract
     * @type String
     */
    ID: "",

    /**
     * Gets the data of the IFrame.
     * @abstract
     * @type String
     */
    Data: ""
};