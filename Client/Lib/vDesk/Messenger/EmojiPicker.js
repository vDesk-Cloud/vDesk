"use strict";
/**
 * Fired if an emoji has been selected.
 * @event vDesk.Messenger.EmojiPicker#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Messenger.EmojiPicker} detail.sender The current instance of the EmojiPicker.
 * @property {String} detail.emoji The selected emoji.
 */
/**
 * Initializes a new instance of the EmojiPicker class.
 * @class Class that represents a simple EMOJI picker.
 * @memberOf vDesk.Messenger
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Messenger
 */
vDesk.Messenger.EmojiPicker = function EmojiPicker() {

    /**
     * Flag indicating whether the emoji list of the EmojiPicker is visible.
     * @type {boolean}
     */
    let Visible = false;

    Object.defineProperties(this, {
        Control: {
            get: () => Control
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickToggle = () => {
        if(Visible){
            Emojis.style.display = "none";
        }else{
            Emojis.style.display = "block";
        }
        Visible = !Visible;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Messenger.EmojiPicker#select
     * @param {MouseEvent} Event
     */
    const OnClickEmoji = Event => new vDesk.Events.BubblingEvent("select", {
        sender: this,
        emoji:  Event.target.textContent
    }).Dispatch(Control);

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "EmojiPicker";

    /**
     * The toggle button of the EmojiPicker.
     * @type {HTMLButtonElement}
     */
    const Toggle = document.createElement("button");
    Toggle.className = "Toggle Button Icon";
    Toggle.style.backgroundImage = `url("${vDesk.Visual.Icons.Messenger.Emoji}")`;
    Toggle.addEventListener("click", OnClickToggle);
    Control.appendChild(Toggle);

    /**
     * The emoji container of the EmojiPicker.
     * @type {HTMLDivElement}
     */
    const Emojis = document.createElement("div");
    Emojis.className = "Emojis";
    Emojis.style.display = "none";
    for(let Codepoint = 0x1F600; Codepoint <= 0x1F64F; Codepoint++){
        const Emoji = document.createElement("span");
        Emoji.className = "Emoji";
        Emoji.textContent = String.fromCodePoint(Codepoint);
        Emoji.addEventListener("click", OnClickEmoji);
        Emojis.appendChild(Emoji);
    }
    Control.appendChild(Emojis);
};
