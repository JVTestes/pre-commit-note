<?php

namespace PreCommitNote\Composer\Script;

class Prompt
{
    protected $tty;

    public function __construct() {
        if (substr(PHP_OS, 0, 3) == "WIN") {
            $this->tty = fopen("\CON", "rb");
        } else {
            if (!($this->tty = fopen("/dev/tty", "r"))) {
                $this->tty = fopen("php://stdin", "r");
            }
        }
    }

    public function get($length = 1024) {
        $result = trim(fgets($this->tty, $length));
        return $result;
    }
}
