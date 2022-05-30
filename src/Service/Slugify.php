<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input): string
    {
        $ponctuation = [",",';','!','?',':','"',"'",];

        $input = str_replace([" ", "ç", 'à'], ["-", 'c', 'a'], $input);
        $input = str_replace($ponctuation, "", $input);
        $input = str_replace(["--","---","----","-----"], "-", $input);
        $input = trim($input);
        $input = strtolower($input);

        return $input;
    }
} 
