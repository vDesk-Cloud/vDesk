<?php
declare(strict_types=1);

namespace Modules;

use vDesk\DataProvider;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Search\Results;
use vDesk\Search\ISearch;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Utils\Log;

/**
 * Description of Search
 *
 * @author Kerry
 */
final class Search extends Module {
    
    /**
     * @inheritDoc
     * Stupid PHP...
     */
    public function __construct(?int $ID = null, Collection $Commands = null) {
        parent::__construct($ID, $Commands);
    }
    
    /**
     * Searches vDesk for a given value.
     *
     * @param null|string $Value   The value to search for.
     * @param null|array  $Filters The filters to use.
     *
     * @return \vDesk\Search\Results The found results.
     */
    public static function Search(string $Value = null, array $Filters = null): Results {
        
        $ResultCollection = new Results();
        
        //Loop through passed filters.
        foreach($Filters ?? Command::$Parameters["Filters"] as $Filter) {
            try {
                //Fetch/load module.
                $Module = \vDesk\Modules::Run($Filter["Module"]);
                //Check if the module implements the ISearch-interface.
                if($Module instanceof ISearch) {
                    //Perform searchoperation.
                    $ResultCollection->Merge(
                        $Module::Search(
                            DataProvider::Escape($Value ?? Command::$Parameters["Value"]),
                            $Filter["Name"]
                        )
                    );
                } else {
                    Log::Warn(__METHOD__, "Attempting to perform search operation on Module {$Filter["Module"]} which doesn't implements the ISearch-interface.");
                }
            } catch(\Throwable $Exception) {
                Log::Warn(__METHOD__, $Exception->getMessage());
                continue;
            }
        }
        return $ResultCollection;
    }
    
}
