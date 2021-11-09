<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Article;
use App\Models\Word;
use Illuminate\Support\Facades\DB;

class WordController extends Controller
{
    /**
     * Разбивает текст статьи на слова и добавляет слово в базу
     * и записи в промежуточную таблицу
     *
     * @return
     */
    public function createAll(Article $article, array $uniqueWordsCounts)
    {

        $words = array_keys($uniqueWordsCounts);

        // вставка в таблицу слов
        $wordsTableInsertData = array_map(function ($value) {
            return ['word' => $value];
        }, $words);
        DB::table('words')->insertOrIgnore($wordsTableInsertData);

        // сырой запрос получения id всех слов их статьи
        // wherein работает неточно
        $sql = [];
        foreach ($words as $word) {
            $sql[] = "word='{$word}'";
        }
        $sql = implode(" OR ", $sql);

        // получение id, сохранение в articles_words
        $wordsWithIds = DB::table('words')->whereRaw($sql)->get();
        $articlesWordsTableInsertData = $wordsWithIds->map(function ($item) use ($article, $uniqueWordsCounts) {
            return [
                'word_id' => $item->id,
                'article_id' => $article->id,
                'count' => $uniqueWordsCounts[$item->word]
            ];
        })->all();

        $rowsCount = count($articlesWordsTableInsertData);
        $offset = 0;
        while ($rowsCount > 0) {
            DB::table('articles_words')->insert(array_slice($articlesWordsTableInsertData, $offset, 1000));
            $offset += 1000;
            $rowsCount -= 1000;
        }

        return $uniqueWordsCounts;
    }

    /**
     * Принимает строку-слово и сохраняет в базе даных
     *
     * @return \App\Models\Word или false, если слово не прошло валидацию
     */
    public function create(String $word)
    {
        $validator = $this->validator(['word' => $word]);
        if ($validator->fails()) {
            return false;
        }
        $foundWord = Word::where('word', $word)->first();
        if (!$foundWord) {
            $foundWord = Word::create(['word' => $word]);
        }
        return $foundWord;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data)
    {
        return Validator::make($data, [
            'word' => ['required', 'string', 'max:255'],
        ]);
    }
}
