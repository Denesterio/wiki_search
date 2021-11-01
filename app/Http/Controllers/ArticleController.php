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

        // страницы в массиве под ключом pages
        // в котором ключ каждой статьи - ее id в вики
        // + 'normalized', 'redirects' при перенаправлении
        $articleId = array_key_first($data);
        if ($articleId === -1) {
            return response()->json(['status' => 404, 'message' => 'Статья не найдена'], 404);
        }
        $articleKey = (string) $articleId;

        // запись статьи содержит 'pageid', 'title', 'extract'
        $title = $data[$articleKey]['title'];
        $body = $data[$articleKey]['extract'];
        $link = $api->buildWikiLink($title);

        $articleData = compact('title', 'body', 'link');
        $this->validator($articleData)->validate();

        $article = Article::create($articleData);

        // подсчет количества слов и передача в WordController массива уникальных слов
        // с кол-вом вхождений для заполнения таблиц
        $wc = new WordController();
        $words = $formatter->countWordsInText($body . ' ' . $title);
        $wc->createAll($article, $words);
        $wordsCount = count($words);

        $len = round((strlen($title) + strlen($body)) / 1024, 1);

        return [
            'title' => $article->title,
            'id' => $article->id,
            'length' => $len,
            'link' => $link,
            'words_count' => $wordsCount,
        ];
    }

    public function index()
    {
        // $articles = DB::raw(
        //     'select articles.id, title, link, articles.created_at, COUNT(*) as words_count
        //     from `articles`
        //     left join `articles_words` on articles.id = articles_words.article_id
        //     group by articles.id
        //     order by articles.created_at desc');
        $articles = DB::table('articles')
            ->selectRaw('articles.id, title, link, articles.created_at, COUNT(*) as words_count')
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
        // SELECT word, count, title, articles.id as article_id
        // FROM `articles_words`
        // JOIN `articles` ON articles.id = articles_words.article_id
        // JOIN `words` ON articles_words.word_id = words.id
        // WHERE word='также';

        $query = $request['query'];
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
