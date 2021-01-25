<?php
declare(strict_types=1);

namespace vDesk\Calendar\Event;

/**
 * Enumeration of participation states.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Participation {
    
    /**
     * Determines if the Participants has not responded on the meeting invitaion yet.
     */
    public const NotResponed = 0;
    
    /**
     *
     */
    public const Confirmed = 1;
    
    /**
     *
     */
    public const WithReservation = 2;
    
    /**
     *
     */
    public const Declined = 3;
    
}
