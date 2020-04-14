<?php
declare(strict_types=1);

namespace vDesk\Search;

/**
 * Provides mechanisms for searching objects.
 *
 * @package vDesk\Search
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
interface ISearch {

    /**
     * Performs a searchoperation.
     *
     * @param string $Value  The value to search for.
     * @param string $Filter The filter of the module to use.
     */
    public static function Search(string $Value, string $Filter = null);
}
