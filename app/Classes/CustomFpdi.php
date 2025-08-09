<?php

namespace App\Classes;

use setasign\Fpdi\Fpdi;

class CustomFpdi extends Fpdi
{
    public function getReader($sourceFileIndex)
    {
        return $this->getPdfReader($sourceFileIndex);
    }
}
