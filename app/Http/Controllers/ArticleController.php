<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\API;
use App\Models\Article;
use Illuminate\Support\Facades\Validator;
use App\Services\TextService;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function create(Request $request,  API $api, TextService $formatter)
    {
        $request->validate([
            'query' => ['required'],
        ]);

        $dataCollect = $api->wikiFetch($request['query']);

        $data = $dataCollect['query']['pages'];
        $articleId = array_key_first($data);
        if ($articleId === -1) {
            return response()->json(['status' => 404, 'message' => 'Статья не найдена'], 404);
        }
        $articleKey = (string) $articleId;

        // создание статьи
        $title = $data[$articleKey]['title'];
        $body = $data[$articleKey]['extract'];
        $link = rawurldecode($data[$articleKey]['fullurl']);

        $articleData = compact('title', 'body', 'link');
        $this->validator($articleData)->validate();

        $article = Article::create($articleData);

        // подсчет количества слов и передача в WordController массива уникальных слов
        // с кол-вом вхождений для заполнения таблиц
        $wc = new WordController();
        $words = $formatter->countWordsInText($body . ' ' . $title);
        $wc->createAll($article, $words);
        $wordsCount = array_reduce($words, function ($sum, $num) {
            $sum += $num;
            return $sum;
        }, 0);

        $lenKBytes = round((strlen($title) + strlen($body)) / 1024, 1);

        return [
            'title' => $article->title,
            'id' => $article->id,
            'length' => $lenKBytes,
            'link' => $link,
            'words_count' => $wordsCount,
        ];
    }

    public function index()
    {
        $articles = DB::table('articles')
            ->selectRaw('articles.id, title, link, articles.created_at, SUM(count) as words_count')
            ->join('articles_words', 'articles.id', '=', 'articles_words.article_id')
            ->groupBy('articles.id', 'articles.title', 'articles.link', 'articles.created_at')
            ->orderBy('articles.created_at', 'desc')
            ->get();
        return $articles;
    }

    /**
     * Возвращает статьи, найденные по ключевому слову
     *
     * @return App\Models\Article collection
     */

    public function search(Request $request)
    {
        $query = $request['query'];
        $query = mb_ereg_replace("ё", "е", $query);
        $articles = DB::table('articles_words')
            ->selectRaw('word, count, title, link, articles.id as article_id')
            ->leftJoin('words', 'words.id', '=', 'articles_words.word_id')
            ->where('word', '=', $query)
            ->leftJoin('articles', 'articles.id', '=', 'articles_words.article_id')
            ->orderBy('count', 'desc')
            ->get();
        return $articles;
    }

    public function show($id)
    {
        return Article::find($id);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data)
    {
        return Validator::make($data, [
            'title' => ['required', 'string', 'unique:articles'],
            'link' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);
    }
}
