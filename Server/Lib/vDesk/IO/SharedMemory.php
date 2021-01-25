<?php
declare(strict_types=1);

namespace vDesk\IO;


class SharedMemory {
    
    /**
     * The pointer of the SharedMemory.
     *
     * @var resource
     */
    private $Pointer;
    
    /**
     * SharedMemory constructor.
     *
     * @param int $ID
     */
    public function __construct(int $ID) {
        $this->Pointer = \shmop_open($ID, "c", 0644, 1);
    }
}