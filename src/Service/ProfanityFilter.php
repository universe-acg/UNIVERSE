<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

class ProfanityFilter
{
    private $bannedWords = ['profanity1', 'profanity2', 'profanity3'];

    public function filterProfanity($text)
    {
        foreach ($this->bannedWords as $word) {
            $text = str_ireplace($word, '***', $text);
        }
        return $text;
    }
}