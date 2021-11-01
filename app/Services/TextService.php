<?php

namespace App\Services;

final class TextService
{
    /**
     * Принимает текст и возвращает массив с количеством вхождений каждого слова
     * слова в нижнем регистре
     *
     * @return Array
     *
     */
    public function countWordsInText(String $text)
    {
        $pattern = '/[a-zA-Zа-яА-Я0-9\p{M}ёЁ]+/u';
        // Убрать ударения и ё
        $formattedText = preg_replace('/\x{0301}/u', '', $text);
        $formattedText = mb_ereg_replace("[ёЁ]", "е", $formattedText);
        preg_match_all($pattern, $formattedText, $matches);

        $wordsCountArray = array();

        foreach ($matches[0] as $word) {
            $wordInLowerCase = mb_strtolower($word);
            if (array_key_exists($wordInLowerCase, $wordsCountArray)) {
                $wordsCountArray[$wordInLowerCase] += 1;
            } else {
                $wordsCountArray[$wordInLowerCase] = 1;
            }
        }

        return $wordsCountArray;
    }
}
