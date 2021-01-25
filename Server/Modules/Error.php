<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Modules\Module;
use vDesk\Pages\Functions;
use vDesk\Pages\Response;

/**
 * Example class for errorhandling controllers.
 * This Controller is shipped by standard with the Framework.
 *
 * @author  Kerry Holz <galeon@artforge.eu>.
 */
class Error extends Module {
    
    /**
     * @param \Throwable $Exception
     *
     * @return mixed
     */
    public static function Index(\Throwable $Exception) {
        Response::$Code = 500;
        return Functions::Template(
            "Error",
            ["Exception" => $Exception]
        );
    }
    
    public static function NotFound(\Throwable $Exception) {
        Response::$Code = 404;
        return Functions::Template(
            "NotFound",
            ["Exception" => $Exception]
        );
    }
    
}