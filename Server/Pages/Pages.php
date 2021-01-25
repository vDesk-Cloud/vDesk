<?php
declare(strict_types=1);

use vDesk\Pages\Page;

/**
 * Class vDesk
 *
 * @author Kerry <DevelopmentHero@gmail.com>
 */
class Pages extends Page {
    /**
     * Initializes a new instance of the View class.
     *
     * @param array         $Values Initializes the View with the specified set of values.
     * @param null|iterable $Templates
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = []) {
        parent::__construct($Values, $Templates);
        $this->Templates->Add("Pages");
    }
}