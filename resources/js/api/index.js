import axios from "axios";

const HOST = "";

const routes = {
    articles: () => [HOST, "articles"].join("/"),
    article: (id) => [HOST, "articles", id].join("/"),
    search: (query) => [HOST, encodeURI(`search/?query=${query}`)].join("/"),
};

const buildRequest = (client, type, path, params = {}) =>
    client[type](path, params);

const makeRequest = (type, path, params) =>
    buildRequest(axios, type, path, params);

const get = (target, id) => makeRequest("get", routes[target](id));
const search = (query) => makeRequest("get", routes.search(query));
const post = (target, params) => makeRequest("post", routes[target](), params);

export default {
    get,
    search,
    post,
};
