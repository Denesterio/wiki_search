<template>
  <div>
    <!-- форма -->
    <form-component
      v-model="currentSearchedKeyWord"
      buttonLabel="Найти"
      @submit.native="findArticles"
      :disabled="isFormDisabled"
    ></form-component>
    <hr />
    <!-- результаты поиска -->
    <div v-if="fetchStatus === 'failed'" class="container">
      <p class="text-danger">{{ error }}</p>
    </div>
    <div v-if="fetchStatus === 'success'" class="container">
      <!-- список -->
      <p>Слово: {{ lastFoundKeyword }}</p>
      <p>Всего совпадений: {{ totalMatchesCount }}</p>
      <ol v-if="foundArticles.length">
        <list-item
          v-for="article in foundArticles"
          :key="article.article_id"
          :article="article"
        />
      </ol>
      <p v-else-if="fetchStatus === 'failed'" class="text-danger">
        {{ error }}
      </p>
    </div>
  </div>
</template>

<script>
import ListItem from "./ListItem.vue";
import handleErrorsMixin from "../mixins/handleErrorsMixin.js";
import api from "../api";
export default {
  name: "find-article",
  components: { ListItem },
  mixins: [handleErrorsMixin], // data: error, methods: handleErrors, handleEmptyInputError
  data() {
    return {
      currentSearchedKeyWord: "",
      foundArticles: [], // результат поиска
      lastFoundKeyword: "", // запрос записывается сюда для отображения в результатах
      // процесс запроса статьи: watching, pending, success, failed
      fetchStatus: "waiting",
    };
  },

  computed: {
    isFormDisabled() {
      return this.fetchStatus === "pending";
    },

    totalMatchesCount() {
      return this.foundArticles.reduce(
        (sum, current) => (sum += current.count),
        0
      );
    },
  },

  methods: {
    findArticles(e) {
      e.preventDefault();
      this.handleEmptyInputError(this.currentSearchedKeyWord);

      this.fetchStatus = "pending";
      api
        .search(this.currentSearchedKeyWord.toLowerCase())
        .then((response) => {
          this.foundArticles = response.data;
          this.fetchStatus = "success";
          this.lastFoundKeyword = this.currentSearchedKeyWord.toLowerCase();
          this.currentSearchedKeyWord = "";
        })
        .catch((error) => {
          this.error = error.message;
          this.fetchStatus = "failed";
        });
    },
  },
};
</script>