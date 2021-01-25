<?php
declare(strict_types=1);

namespace vDesk\Messenger;

use vDesk\DataProvider;
use vDesk\Data\IDataView;
use vDesk\Data\IModel;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Collection;
use vDesk\Struct\Properties;

/**
 * Class ChatRoom represents ...
 *
 * @package vDesk\Messenger
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class ChatRoom implements IModel {
    
    use Properties;
    
    /**
     * The ID of the ChatRoom.
     *
     * @var int|null
     */
    private $ID;
    
    /**
     * The name of the ChatRoom.
     *
     * @var string
     */
    private $Name = "";
    
    /**
     * The current members of the ChatRoom.
     *
     * @var \vDesk\Struct\Collections\Typed\Collection|null
     */
    private $Members;
    
    
    /**
     * Flag indicating whether the members of the ChatRoom have been changed.
     *
     * @var bool
     */
    private $MembersChanged = false;
    
    /**
     * Initializes a new instance of the ChatRoom class.
     *
     * @param int|null $ID       The ID of an existing ChatRoom.
     * @param bool     $AutoFill Determines whether the ChatRoom should be filled by creation.
     */
    public function __construct( ?int $ID = null, bool $AutoFill = false) {
        $this->ID = $ID;
        $this->AddProperties([
            "Members" => [
                Get => function(): ?Collection {
                    if($this->Members === null && $this->ID !== null) {
                        //   foreach(DataProvider::Execute("SELECT UserID"))
                        $this->Members = new Collection();
                    }
                    return $this->Members;
                }
            ]
        ]);
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->ID;
    }
    
    /**
     * Creates an IDataView from a JSON-encodable representation.
     *
     * @param mixed $DataView The Data to use to create an instance of the IDataView.
     *                        The type and format should match the output of @return \vDesk\Data\IDataView An instance of the implementing
     *                        class filled with the provided data.
     *
     * @see \vDesk\Data\IDataView::ToDataView().
     *
     */
    public static function FromDataView(mixed $DataView): IDataView {
        // TODO: Implement FromDataView() method.
    }
    
    /**
     * Generates a JSON-encodable representation of the IDataView.
     *
     * @return mixed The JSON-encodable representation of the IDataView.
     */
    public function ToDataView() {
        // TODO: Implement ToDataView() method.
    }
    
    /**
     * Saves possible changes of an existing IModel or creates a new one.
     */
    public function Save(): void {
        // TODO: Implement Save() method.
    }
    
    /**
     * Deletes this IModel.
     */
    public function Delete(): void {
        // TODO: Implement Delete() method.
    }
    
    /**
     * Fills the model with all values if a valid ID was supplied.
     */
    public function Fill(): void {
        // TODO: Implement Fill() method.
    }
    
    public function Join(...$Users): void {
    
    }
}