/**
 * Static class that provides utility methods that perform common tasks involving nodes in a visual tree.
 * @namespace TreeHelper
 * @memberOf vDesk.Visual
 */
vDesk.Visual.TreeHelper = {

    /**
     * Gets the offset of a specified DOM-Node relative to the window.
     * @param {HTMLElement} Node The node whose offset is returned.
     * @return {Object} An object that represents the offset of the specified node. {top: [], left: []}.
     */
    GetOffset: function(Node) {
        let LeftOffset = Node.offsetLeft;
        let TopOffset = Node.offsetTop;
        for(const Parent of this.OffsetParents(Node)) {
            TopOffset += Parent.offsetTop;
            LeftOffset += Parent.offsetLeft;
        }
        return {
            left: LeftOffset,
            top:  TopOffset
        };
    },

    /**
     * Gets the left offset of a specified DOM-Node relative to the window.
     * @param {HTMLElement} Node The node whose left offset is returned.
     * @return {Number} The left offset off the specified node in pixels.
     */
    GetLeftOffset: function(Node) {
        let Offset = Node.offsetLeft;
        for(const Parent of this.OffsetParents(Node)) {
            Offset += Parent.offsetLeft;
        }
        return Offset;
    },

    /**
     * Gets the top offset of a specified DOM-Node relative to the window.
     * @param {HTMLElement} Node The node whose top offset is returned.
     * @return {Number} The top offset off the specified node in pixels.
     */
    GetTopOffset: function(Node) {
        let Offset = Node.offsetTop;
        for(const Parent of this.OffsetParents(Node)) {
            Offset += Parent.offsetTop;
        }
        return Offset;
    },

    /**
     * Determines whether 2 elements visually collide/overlap.
     * @param {HTMLElement} First The first element to check.
     * @param {HTMLElement} Second THe second element to check.
     * @return {Boolean} True if the specified elements visually collide/overlap; otherwise, false.
     */
    HitTest: function(First, Second) {
        //Cache values to reduce reflows on every access.
        const {offsetTop: FirstOffsetTop, offsetLeft: FirstOffsetLeft} = First;
        const {offsetTop: SecondOffsetTop, offsetLeft: SecondOffsetLeft} = Second;

        //Perform hittest.
        return !(((FirstOffsetTop + First.offsetHeight) < (SecondOffsetTop))
                 || (FirstOffsetTop > (SecondOffsetTop + Second.offsetHeight))
                 || ((FirstOffsetLeft + First.offsetWidth) < SecondOffsetLeft)
                 || (FirstOffsetLeft > (SecondOffsetLeft + Second.offsetWidth)));
    },

    /**
     * Generator that iterates over the offset-parents of a DOM-Node until it reaches the top-most DOM-Node within the document.
     * @param {HTMLElement} Node The node off which the offset-parents will get fetched.
     * @yields {HTMLElement} The next offset-parent of the specified DOM-Node.
     */
    OffsetParents: function* (Node) {
        for(Node = Node.offsetParent; Node !== null; Node = Node.offsetParent) {
            yield Node
        }
    },

    /**
     * Generator that iterates over the child Nodes of a specified parent Node.
     * @param {Node} Node The Node to iterate over the children of.
     * @param {Number} Filter The filter that specifies the type of Node that the Generator will yield.
     * @param {Function} [Callback=null] A Callback to apply to determine the current Node is valid.
     * @return {IterableIterator<Node>} A Generator that yields the child Nodes of the specified parent Node.
     */
    IterateChildren: function* (Node, Filter = NodeFilter.SHOW_ELEMENT, Callback = () => NodeFilter.FILTER_ACCEPT) {
        const Iterator = document.createNodeIterator(Node, Filter, {acceptNode: Callback});
        for(let Node = Iterator.nextNode(); Node !== null; Node = Iterator.nextNode()) {
            yield Node;
        }
    },

    /**
     * Generator that iterates over the child Nodes of a specified parent Node.
     * @param {Node} Node The Node to iterate over the children of.
     * @param {Number} Filter The filter that specifies the type of Node that the Generator will yield.
     * @param {Function} [Callback=null] A Callback to apply to determine the current Node is valid.
     * @return {IterableIterator<Node>} A Generator that yields the child Nodes of the specified parent Node.
     */
    WalkChildren: function* (Node, Filter = NodeFilter.SHOW_ELEMENT, Callback = () => NodeFilter.FILTER_ACCEPT) {
        const Iterator = document.createTreeWalker(Node, Filter, {acceptNode: Callback});
        for(let Node = Iterator.nextNode(); Node !== null; Node = Iterator.nextNode()) {
            yield Node;
        }
    }
};