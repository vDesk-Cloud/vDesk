<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

/**
 * Interface for observable Collections and Dictionaries.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IObservable extends IEnumerable {

    /**
     * Name of events dispatched while new elements are being added to the IObservable.
     */
    public const Add = "Add";

    /**
     * Name of events dispatched while elements are being removed from the IObservable.
     */
    public const Remove = "Remove";

    /**
     * Name of events dispatched while elements of the IObservable are being replaced.
     */
    public const Replace = "Replace";

    /**
     * Name of events dispatched when the IObservable is being cleared.
     */
    public const Clear = "Clear";

    /**
     * Initializes a new instance of the IObservable class.
     *
     * @param iterable $Elements Initializes the IObservable with the specified set or map of elements.
     * @param iterable $Add      Initializes the IObservable with the specified set of callables that listen on the "Add" event.
     * @param iterable $Remove   Initializes the IObservable with the specified set of callables that listen on the "Remove" event.
     * @param iterable $Replace  Initializes the IObservable with the specified set of callables that listen on the "Replace" event.
     * @param iterable $Clear    Initializes the IObservable with the specified set of callables that listen on the "Clear" event.
     */
    public function __construct(iterable $Elements = [], iterable $Add = [], iterable $Remove = [], iterable $Replace = [], iterable $Clear = []);

    /**
     * Enables or disables dispatching of events on the IObservable.
     *
     * @param null|bool $Dispatching Flag indicating whether to dispatch events.
     *
     * @return bool Flag indicating whether events are currently being dispatched.
     */
    public function Dispatching(?bool $Dispatching): bool;

    /**
     * Registers a callable as an eventlistener for a specified event on the IObservable.
     *
     * @param string   $Event    The name of the event to listen for.
     * @param callable $Listener The eventlistener to register.
     */
    public function AddEventListener(string $Event, callable $Listener): void;

    /**
     * Removes an eventlistener from the IObservable.
     *
     * @param string   $Event    The name of the event of the eventlistener to remove.
     * @param callable $Listener The eventlistener to remove.
     */
    public function RemoveEventListener(string $Event, callable $Listener): void;
}