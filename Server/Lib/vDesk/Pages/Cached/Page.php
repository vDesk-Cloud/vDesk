<?php
declare(strict_types=1);

namespace vDesk\Pages\Cached;

use vDesk\Configuration\Settings;
use vDesk\IO\File;
use vDesk\IO\FileStream;
use vDesk\IO\Path;
use vDesk\IO\Stream\Mode;

/**
 * Baseclass for cached Pages.
 *
 * @author  Kerry Holz <k.holz@artforge.eu>.
 */
abstract class Page extends \vDesk\Pages\Page {
    
    /**
     * @inheritDoc
     */
    public function ToDataView(): string {
        $File = Settings::$Local["Pages"]["Cache"] . Path::Separator . \str_replace("\\", ".", static::class) . ".html";
        if(File::Exists($File)) {
            return \file_get_contents($File);
        }
        $File    = new FileStream($File, Mode::Write | Mode::Truncate | Mode::Binary);
        $Content = parent::ToDataView();
        $File->Write($Content);
        return $Content;
    }
    
}