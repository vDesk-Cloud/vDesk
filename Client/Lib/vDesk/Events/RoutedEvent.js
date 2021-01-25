"use strict";
/**
 * Initializes a new instance of the RoutedEvent class.
 * @class Represents a routed event that traverses down the domtree instead of bubbling up.
 * Routed events are getting dispatched to all lowest child-descendants of a specified dom-node.
 * Every attached eventlistener between the target dom-node and a bottommost dom-node that listens while the capture-phase gets triggered as often as the target dom-node has descendants.
 * This happens for every eventlistener that listens while the bubbling-phase either if the RoutedEvent can bubble.
 * @param {String} Type Sets the identifying type of the RoutedEvent.
 * @param {Object} [Arguments={}] Set the event arguments of the RoutedEvent.
 * @param {Boolean} [Cancelable=true] Flag indicating whether the RoutedEvent can be canceled.
 * @param {Boolean} [Bubbling=false] Flag indicating whether the RoutedEvent bubbles after it has been dispatched to a descendant of its eventtarget.
 * In fact that this class uses furthermore standard dom-events, omitting or setting the value to false would reduce the workload by half.
 * @memberOf vDesk.Events
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Events.RoutedEvent = function RoutedEvent(Type, Arguments = {}, Cancelable = true, Bubbling = false) {

    /**
     * The event of the RoutedEvent.
     * @type CustomEvent
     * @ignore
     */
    const Event = vDesk.Events.Create(Type, Arguments, Cancelable, Bubbling);

    /**
     * Dispatches the event from the specified DOM-Node to its lowest descendants.
     * Because of the Event's traversing nature, the RoutedEvent can only be dispatched to elements which implement the Node-interface.
     * @param {Node|HTMLElement} [Node=document.body] The Node to start the event-routing.
     */
    this.Dispatch = function(Node) {

        //Dispatch event to the descendants of the specified node until its being canceled.
        for(const Child of vDesk.Events.RoutedEvent.Traverse(Node)) {
            if(Event.Canceled) {
                break;
            }
            Child.dispatchEvent(Event);
        }
    };
};

/**
 * Traverses down the documenttree beginning at the specified root-node and fetches all lowest descendants.
 * @param {Node|HTMLElement} [Node = document.body] The node to start the traversal from.
 * @return {Generator} A generator that yields every lowest descendants.
 */
vDesk.Events.RoutedEvent.Traverse = function* (Node) {

    const NodeIterator = document.createNodeIterator(
        Node ?? document.body,
        NodeFilter.SHOW_ELEMENT,
        {acceptNode: Node => Node.children.length > 0 ? NodeFilter.FILTER_SKIP : NodeFilter.FILTER_ACCEPT},
        false
    );
    for(let CurrentNode = NodeIterator.nextNode(); CurrentNode !== null; CurrentNode = NodeIterator.nextNode()) {
        yield CurrentNode;
    }
};