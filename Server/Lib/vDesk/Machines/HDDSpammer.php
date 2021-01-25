<?php
declare(strict_types=1);

namespace vDesk\Machines;

use vDesk\IO\FileStream;
use vDesk\IO\Path;
use vDesk\IO\Stream\Mode;

class HDDSpammer extends Machine {
    
    /**
     * Targetfile.
     *
     * @var null|\vDesk\IO\FileStream
     */
    private $File = null;
    
    public function Start(): void {
        $this->File = new FileStream(\Server . Path::Separator . "penis.txt", Mode::Append | Mode::Binary);
        $this->File->Write("Let's spam a bit!" . PHP_EOL);
    }
    
    public function Run(): void {
        
        $this->File->Write("Spam!" . PHP_EOL);
        \sleep(10);
        
    }
    
    public function Suspend(): void {
        $this->File->Write("Got suspended at : . " . (new \DateTime())->format(\DateTime::ATOM) . PHP_EOL);
    }
    
    public function Resume(): void {
        $this->File->Write("Got resumed at : . " . (new \DateTime())->format(\DateTime::ATOM) . PHP_EOL);
    }
    
    public function Stop(int $Code = 0): void {
        $this->File->Write("Ferdammden Arschlöcha!" . PHP_EOL);
        $this->File->Close();
        parent::Stop($Code);
    }
}