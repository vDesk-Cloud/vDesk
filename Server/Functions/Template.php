<?php
declare(strict_types=1);

use vDesk\Configuration\Settings;
use vDesk\IO\File;
use vDesk\IO\FileNotFoundException;
use vDesk\IO\Path;

/**
 * Loads and evaluates a template-file.
 *
 * @param string $Template The template to load.
 * @param array  $Values   The values to populate to the specified template.
 *
 * @return string The Hypertext-markup of the loaded template.
 * @throws FileNotFoundException Thrown if the file of the specified template does not exist.
 */
function Template(string $Template, array $Values = []): string {
    \extract($Values, \EXTR_OVERWRITE);
    $Path = Settings::$Local["Pages"]["Templates"] . Path::Separator . $Template . ".php";
    
    if(!File::Exists($Path)) {
        throw new FileNotFoundException("File of template '$Template' doesn't exist!");
    }
    \ob_start();
    include $Path;
    return \ob_get_clean();
}