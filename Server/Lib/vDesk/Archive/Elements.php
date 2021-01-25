<?php
declare(strict_types=1);

namespace vDesk\Archive;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\AccessControlList;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of {@link \vDesk\Archive\Element} Elements.
 *
 * @package vDesk\Archive
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Elements extends Collection implements IDataView {
    
    /**
     * The Type of the Elements.
     */
    public const Type = Element::class;
    
    /**
     * Fetches the child Elements of a specified parent Element.
     *
     * @param \vDesk\Archive\Element $Parent The parent Element to fetch the children of.
     *
     * @return \vDesk\Archive\Elements A Collection containing every child Element of the specified parent Element.
     */
    public static function FromElement(Element $Parent): self {
        return new static(
            (static function() use ($Parent) {
                foreach(
                    Expression::Select("*")
                              ->From("Archive.Elements")
                              ->Where(["Parent" => $Parent])
                    as
                    $Element
                ) {
                    yield                     new Element(
                        (int)$Element["ID"],
                        new User((int)$Element["Owner"]),
                        $Parent,
                        $Element["Name"],
                        (int)$Element["Type"],
                        new \DateTime($Element["CreationTime"]),
                        null,
                        $Element["Extension"],
                        $Element["File"],
                        (int)$Element["Size"],
                        $Element["Thumbnail"],
                        new AccessControlList([], (int)$Element["AccessControlList"])
                    );
                }
            })()
        );
    }
    
    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Element {
        return parent::Find($Predicate);
    }
    
    /**
     * @inheritdoc
     */
    public function Remove($Element): Element {
        return parent::Remove($Element);
    }
    
    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Element {
        return parent::RemoveAt($Index);
    }
    
    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Element {
        return parent::offsetGet($Index);
    }
    
    /**
     * Creates a Elements from a specified data view.
     *
     * @param array $DataView The data to use to create a Elements.
     *
     * @return \vDesk\Archive\Elements A Elements created from the specified data view.
     */
    public static function FromDataView($DataView = []): Elements {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Element::FromDataView($Data);
                }
            })()
        );
    }
    
    /**
     * Creates a data view of the Elements.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Elements.
     *
     * @return array The data view representing the current state of the Elements.
     */
    public function ToDataView(bool $Reference = false): array {
        return $this->Reduce(
            static function(array $Elements, Element $Element) use ($Reference): array {
                $Elements[] = $Element->ToDataView($Reference);
                return $Elements;
            },
            []
        );
    }
    
}