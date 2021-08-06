<?php
declare(strict_types=1);

namespace vDesk;


use vDesk\Locale\LocaleDictionary;

class Locale extends Struct\StaticSingleton {

    public static ?LocaleDictionary $Translations;

    /**
     * Initializes a new instance of the DataProvider class.
     *
     * @param string      $Provider Initializes the DataProvider with the specified data provider.
     * @param string      $Server   Initializes the DataProvider with the specified server address.
     * @param int|null    $Port     Initializes the DataProvider with the specified port.
     * @param string      $User     Initializes the DataProvider with the specified database user.
     * @param string      $Password Initializes the DataProvider with the specified password of the database user.
     * @param string|null $Charset  Initializes the DataProvider with the specified charset.
     */
    protected static function _construct(
        string $Provider = "",
        string $Server = "localhost",
        int $Port = null,
        string $User = "",
        string $Password = "",
        string $Charset = null
    ) {

    }

}