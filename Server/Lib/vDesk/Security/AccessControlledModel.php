<?php
declare(strict_types=1);

namespace vDesk\Security;

use vDesk\Data\IModel;
use vDesk\Security\AccessControlList\Entry;
use vDesk\Struct\Properties;

/**
 * Represents an access controlled database model which allows to verify.
 *
 * @property null|\vDesk\Security\AccessControlList AccessControlList Gets or sets the AccessControlList  of the AccessControlledModel.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
abstract class AccessControlledModel implements IModel {
    
    use Properties;
    
    /**
     * The AccessControlList of the AccessControlledModel.
     *
     * @var \vDesk\Security\AccessControlList|null
     */
    private ?AccessControlList $AccessControlList;
    
    
    /**
     * The ID of the AccessControlList of the AccessControlledModel.
     * This property can be used as a cache for the return value of the abstract AccessControlledModel::GetACLID method.
     *
     * @var null|int
     */
    protected ?int $ACLID;
    
    /**
     * Initializes a new instance of the AccessControlledModel class.
     *
     * @param null|AccessControlList $AccessControlList Initializes the AccessControlledModel with the specified AccessControlList.
     */
    public function __construct($AccessControlList = null) {
        $this->AccessControlList = $AccessControlList;
        $this->AddProperties([
            "AccessControlList" => [
                \Get => fn(): ?AccessControlList => $this->AccessControlList ??= new AccessControlList([], $this->GetACLID()),
                \Set => function(AccessControlList $Value) {
                    if($this->AccessControlList !== null) {
                        if($Value->ID !== null) {
                            $Value->Fill();
                        }
                        
                        $this->AccessControlList->Clear();
                        
                        //Copy Entries from passed ACL.
                        foreach($Value as $Entry) {
                            $Copy         = new Entry();
                            $Copy->Read   = $Entry->Read;
                            $Copy->Write  = $Entry->Write;
                            $Copy->Delete = $Entry->Delete;
                            $this->AccessControlList->Add($Copy);
                        }
                    } else {
                        $this->AccessControlList = $Value;
                    }
                }
            ]
        ]);
    }
    
    /**
     * Deletes the associated database record of the AccessControlList of the AccessControlledModel.
     *
     * @param \vDesk\Security\User|null $User The User to check optionally for delete permissions.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if a specified User has not the permission to delete the AccessControlledModel.
     */
    public function Delete(User $User = null): void {
        if($this->AccessControlList !== null) {
            if($User !== null && !$this->AccessControlList->CanDelete($User)) {
                throw new UnauthorizedAccessException();
            }
            $this->AccessControlList->Delete();
        }
    }
    
    /**
     * Saves possible changes of a non-virtual AccessControlList of the AccessControlledModel to the associated database record or creates a new one.
     *
     * @param \vDesk\Security\User|null $User The User to check optionally for write permissions on non virtual AccessControlledModels.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException  Thrown if a specified User has not the permission to write the AccessControlledModel.
     */
    public function Save(User $User = null): void {
        if($this->AccessControlList !== null) {
            if($User !== null && !$this->AccessControlList->CanWrite($User)) {
                throw new UnauthorizedAccessException();
            }
            $this->AccessControlList->Save();
        } else {
            $this->AccessControlList = new AccessControlList(
                [
                    Entry::FromUser(),
                    Entry::FromGroup(),
                    Entry::FromUser($User)
                ]
            );
        }
    }
    
    /**
     * Fills the AccessControlList of the AccessControlledModel with the values of the associated database record.
     *
     * @param \vDesk\Security\User|null $User The User to check optionally for write permissions on non virtual AccessControlledModels.
     *
     * @return \vDesk\Security\AccessControlledModel The current instance for further chaining.
     * @throws \vDesk\Security\UnauthorizedAccessException  Thrown if a specified User has not the permission to write the AccessControlledModel.
     */
    public function Fill(User $User = null): AccessControlledModel {
        $this->AccessControlList ??= new AccessControlList([], $this->GetACLID());
        $this->AccessControlList->Fill($User);
        if($User !== null && !$this->AccessControlList->Read) {
            throw new UnauthorizedAccessException();
        }
        return $this;
    }
    
    /**
     * Gets the ID of the AccessControlList of the current access controlled instance.
     *
     * @return int|null The ID of the current access controlled instance; otherwise, null.
     */
    abstract protected function GetACLID(): ?int;
    
}
