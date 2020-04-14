<?php
declare(strict_types=1);

namespace vDesk\Search;

use vDesk\Data\IDataView;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of {@link \vDesk\Search\Result} objects.
 *
 * @package vDesk\Search
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Results extends Collection implements IDataView {

    /**
     * The Type of the Results.
     */
    public const Type = Result::class;

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Result {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): Result {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Result {
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Result {
        return parent::offsetGet($Index);
    }

    /**
     *
     *
     * @return array
     */
    public function ToDataView(): array {
        return $this->Reduce(static function(array $Results, Result $Result): array {
            $Results[] = $Result->ToDataView();
            return $Results;
        }, []);
    }

    /**
     * Creates an IDataView from a JSON-encodable representation.
     *
     * @param mixed $DataView The Data to use to create an instance of the IDataView.
     *                        The type and format should match the output of @see \vDesk\Data\IDataView::ToDataView().
     *
     * @return \vDesk\Data\IDataView An instance of the implementing class filled with the provided data.
     */
    public static function FromDataView($DataView): IDataView {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Result::FromDataView($Data);
                }
            })()
        );
    }
}
