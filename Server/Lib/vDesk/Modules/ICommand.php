<?php
declare(strict_types=1);

namespace vDesk\Modules;

use vDesk\IO\Input\IProvider;

/**
 * Interface for Commands providing an abstract access to user input.
 *
 * @package vDesk\Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface ICommand {
    
    /**
     * Factory method that creates a new instance of the ICommand class holding the value passed through a specified IProvider.
     *
     * @param \vDesk\IO\Input\IProvider $Provider The input Provider to parse the command and values of.
     *
     * @return \vDesk\Modules\Command A Command holding the values from the specified IProvider.
     */
    public static function Parse(IProvider $Provider): ICommand;
    
    /**
     * Executes the ICommand.
     *
     * @return mixed The result of the execution of the ICommand.
     */
    public function Execute();
    
}