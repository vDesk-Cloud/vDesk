<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

interface IObservable extends IEnumerable {

    public const Add = "Add";

    public const Remove = "Remove";

    public const Replace = "Replace";

    public const Clear = "Clear";


    /**
     * Initializes a new instance of the IObservable class.
     *
     * @param iterable $Elements Initializes the IObservable with the specified set or map of elements.
     * @param iterable $Add      Initializes the IObservable with the specified set of callables that listen of the "Add" event.
     * @param iterable $Remove   Initializes the IObservable with the specified set of callables that listen of the "Remove" event.
     * @param iterable $Replace  Initializes the IObservable with the specified set of callables that listen of the "Replace" event.
     * @param iterable $Clear    Initializes the IObservable with the specified set of callables that listen of the "Clear" event.
     */
    public function __construct(iterable $Elements = [], iterable $Add = [], iterable $Remove = [], iterable $Replace = [], iterable $Clear = []);

    public function AddEventListener(string $Event, callable $Listener);

    public function RemoveEventListener(string $Event, callable $Listener);

    public function StartDispatch();

}