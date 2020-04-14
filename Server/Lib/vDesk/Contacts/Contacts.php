<?php
declare(strict_types=1);

namespace vDesk\Contacts;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\IResult;
use vDesk\Contacts\Contact\Options;
use vDesk\Data\IDataView;
use vDesk\Locale\Country;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of {@link \vDesk\Contacts\Contact} objects.
 *
 * @property int $ID Gets or sets the ID of the associated {@link \vDesk\Contacts\Contact} of the Options.
 * @package Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Contacts extends Collection implements IDataView {

    /**
     * The Type of the Elements.
     */
    public const Type = Contact::class;

    /**
     * Initializes a new instance of the Contacts class.
     *
     * @param iterable|null        $Elements Initializes the Contacts with the specified set of elements.
     * @param \vDesk\Security\User $User     Fills the collection according to the user.
     * @param int                  $From     Specifies a start index within a range of contacts.
     * @param int                  $To       Specifies a end index within a range of contacts.
     */
    public function __construct(?iterable $Elements = [], User $User = null, int $From = null, int $To = null) {
        parent::__construct($Elements);
        if($User !== null) {
            $this->Fill($From, $To);
        }
    }

    /**
     * Fetches a Contacts containing all {@links \vDesk\Contacts\Contact} contacts of a specified {@link
     * \vDesk\Security\User} owner.
     *
     * @param \vDesk\Security\User $User
     *
     * @return \vDesk\Contacts\Contacts A collection containing all Contacts of the specified owner.
     */
    public static function FromOwner(User $User): self {

    }

    /**
     * Creates a new Contacts containing all Contacts whose surname starts with a specified alphabetical letter.
     *
     * @param string $Char   The letter to search.
     * @param int    $Amount The amount of Contacts to fetch.
     * @param int    $Offset The offset to start from.
     *
     * @return \vDesk\Contacts\Contacts A collection containing all Contacts whose sure-name starts with the specified
     *                                             alphabetical letter.
     */
    public static function StartsWith(string $Char, int $Amount = 100, int $Offset = 0): self {

        $ASCIIValue = \ord(\strtoupper($Char));

        return new static(
            (static function(IResult $ResultSet) {
                //Hydrate.
                foreach($ResultSet as $Row) {
                    $Contact = new Contact((int)$Row["ID"]);
                    if($Contact->AccessControlList->Read) {
                        $Contact->Owner       = new User((int)$Row["Owner"]);
                        $Contact->Gender      = (int)$Row["Gender"];
                        $Contact->Title       = $Row["Title"];
                        $Contact->Forename    = $Row["Forename"];
                        $Contact->Surname     = $Row["Surname"];
                        $Contact->Street      = $Row["Street"];
                        $Contact->HouseNumber = $Row["HouseNumber"];
                        $Contact->ZipCode     = $Row["ZipCode"] !== null ? (int)$Row["ZipCode"] : null;
                        $Contact->City        = $Row["City"];
                        $Contact->Country     = new Country($Row["Country"]);
                        $Contact->Options     = (new Options([], $Contact))->Fill();
                        $Contact->Company     = new Company($Row["Company"] !== null ? (int)$Row["Company"] : null);
                        $Contact->Annotations = $Row["Annotations"];
                        yield $Contact;
                    }
                }
            })
            (Expression::Select("*")
                       ->From("Contacts.Contacts")
                       ->Where([
                           "Surname" => $Condition = $ASCIIValue > 64 && $ASCIIValue < 91
                               ? ["LIKE" => "{$Char}%"]
                               : ["NOT REGEXP" => "^[A-Za-z]"]
                       ])
                       ->Limit($Amount)
                       ->Offset($Offset)
                       ->Execute())

        );
    }

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Contact {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): Contact {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Contact {
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Contact {
        return parent::offsetGet($Index);
    }

    /**
     * Returns a JSON-encodable representation of the Contacts.
     *
     * @return array Contacts.
     */
    public function ToDataView(): array {
        return $this->Reduce(static function(array $Contacts, Contact $Contact): array {
            $Contacts[] = $Contact->ToDataView();
            return $Contacts;
        }, []);
    }

    /**
     * Creates an IDataView from a JSON-encodable representation.
     *
     * @param mixed $DataView The Data to use to create an instance of the IDataView. The type and format should match the output of @see
     *                        \vDesk\Data\IDataView::ToDataView().
     *
     * @return \vDesk\Data\IDataView An instance of the implementing class filled with the provided data.
     */
    public static function FromDataView($DataView): IDataView {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $aoData) {
                    yield Contact::FromDataView($aoData);
                }
            })()
        );
    }

    /**
     * @param int|null $From
     * @param int|null $To
     *
     */
    private function Fill(int $From = null, int $To = null): void {

        $Expression = Expression::Select("*")
                                ->From("Contacts.Contacts");

        //Check if a range has been passed.
        if($From !== null && $To !== null && $To > $From) {
            $Expression->Limit($To - $From)
                       ->Offset($From);
        }

        //Hydrate.
        foreach($Expression as $Row) {
            $Contact = new Contact((int)$Row["ID"]);
            if($Contact->AccessControlList->Read) {
                $Contact->Owner       = new User((int)$Row["Owner"]);
                $Contact->Gender      = (int)$Row["Gender"];
                $Contact->Title       = $Row["Title"];
                $Contact->Forename    = $Row["Forename"];
                $Contact->Surname     = $Row["Surname"];
                $Contact->Street      = $Row["Street"];
                $Contact->HouseNumber = $Row["HouseNumber"];
                $Contact->ZipCode     = (int)$Row["ZipCode"];
                $Contact->City        = $Row["City"];
                $Contact->Country     = new Country($Row["Country"]);
                $Contact->Options     = new Options(null, (int)$Row["ID"], true);
                $Contact->Company     = new Company((int)$Row["Company"]);
                $Contact->Annotations = $Row["Annotations"];
                $this->Add($Contact);
            }
        }
    }
}
