<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

final class API
{
    private const WIKI_HOST = 'https://ru.wikipedia.org';
    private const WIKI_PATH = '/w/api.php';
    private const WIKI_PARAMS = [
        'format' => 'json',
        'action' => 'query',
        'prop' => 'extracts|info',
        'inprop' => 'url|displaytitle',
        'explaintext' => 1,
        'redirects' => 1,
    ];
    private const TIMEOUT = 10;

    /**
     *страницы в массиве под ключом pages
     *в котором ключ каждой статьи - ее id в вики
     *['batchcomplete',
     *'query' => ['normalized' => ['from', 'to'],
     *'pages' => [pageid => [ключи 'pageid', 'title', 'extract', 'touched', 'length', 'fullurl']]]]
     */
    public function wikiFetch(String $query)
    {
        /**
         * Делает запрос к Вики и возвращает обработанный ответ
         *
         * @return Collect
         */
        $params = http_build_query(array_merge(self::WIKI_PARAMS, ['titles' => $query]));
        // $params = implode('&', [self::WIKI_FORMAT, self::WIKI_ACTION, self::WIKI_PROP, "titles={$query}"]);
        $url = self::WIKI_HOST . self::WIKI_PATH . '?' . $params;
        $response = HTTP::timeout(self::TIMEOUT)->get($url);
        return $response->throw()->collect();
    }
}
