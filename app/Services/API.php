<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

final class API
{
    private const WIKI_HOST = 'https://ru.wikipedia.org';
    private const WIKI_PATH = '/w/api.php';
    private const WIKI_FORMAT = 'format=json';
    private const WIKI_ACTION = 'action=query';
    private const WIKI_PARAMS = 'prop=extracts&explaintext&redirects=1';
    private const WIKI_CONTENT_PATH = 'wiki';
    private const TIMEOUT = 10;

    public function wikiFetch(String $query)
    {
        /**
         * Делает запрос к Вики и возвращает ответ в json
         *
         * @return Array
         * с ключами ['batchcomplete', 'query']
         */
        $params = implode('&', [self::WIKI_FORMAT, self::WIKI_ACTION, self::WIKI_PARAMS, "titles={$query}"]);
        $url = self::WIKI_HOST . self::WIKI_PATH . '?' . $params;
        $response = HTTP::timeout(self::TIMEOUT)->get($url);
        return $response->throw()->collect();
    }

    /**
     * Формирует ссылку на статью на основе названия
     *
     * @return String
     */
    public function buildWikiLink(String $title)
    {
        $formattedTitle = str_replace(' ', '_', $title);
        $link = implode('/', [self::WIKI_HOST, self::WIKI_CONTENT_PATH, $formattedTitle]);
        return $link;
    }
}
