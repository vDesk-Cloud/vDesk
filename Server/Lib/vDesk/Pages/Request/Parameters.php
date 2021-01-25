<?php
declare(strict_types=1);

namespace vDesk\Pages\Request;

use vDesk\IO\Input\IProvider;
use vDesk\Struct\Collections\Dictionary;

/**
 * Class that represents a CGI and messagebody parameter dictionary.
 *
 * @package vDesk\Pages\Request
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Parameters extends Dictionary {
    
    /**
     * The input Provider of the Parameters.
     *
     * @var null|\vDesk\IO\Input\IProvider
     */
    private ?IProvider $Provider = null;
    
    /**
     * Initializes a new instance of the Parameters.
     *
     * @param iterable                       $Parameters Initializes the Parameters with the specified set of parameters.
     * @param \vDesk\IO\Input\IProvider|null $Provider   Initializes the Parameters with the specified input Provider.
     */
    public function __construct(iterable $Parameters = [], IProvider $Provider = null) {
        parent::__construct($Parameters);
        $this->Provider = $Provider;
    }
    
    /**
     * {@inheritDoc}
     */
    public function offsetGet($Key) {
        if($this->offsetExists($Key)) {
            return parent::offsetGet($Key);
        }
        $this->Add($Key, $this->Provider->ParseCommand($Key) ?? $this->Provider->ParseParameter($Key));
        return $this->offsetGet($Key);
    }
}