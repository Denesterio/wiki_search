<template>
  <div>
    <!-- форма -->
    <form-component
      v-model="currentSearchedArticle"
      @submit.native="loadArticle"
      buttonLabel="Скопировать"
      :disabled="isFormDisabled"
    ></form-component>
    <!-- показ данных о загрузке -->
    <section class="border mt-4 p-3 load-info">
      <svg-loading-spinner v-if="fetchStatus === 'pending'" />
      <p v-else-if="fetchStatus === 'failed'" class="text-danger">
        Не удалось загрузить страницу ({{ error }})
      </p>
      <article-info
        v-else-if="fetchStatus === 'success'"
        :article="articleInfo"
      />
    </section>
    <!-- таблица -->
    <section class="mt-4">
      <h2 v-if="loading">Загрузка...</h2>
      <h2 v-if="error.lenght">Ошибка во время загрузки</h2>
      <template v-else-if="articles.length > 0">
        <h2>Загруженные статьи</h2>
        <base-table :articles="articles"></base-table>
      </template>
    </section>
  </div>
</template>

<script>
import SvgLoadingSpinner from "./SvgLoadingSpinner.vue";
import ArticleInfo from "./ArticleInfo.vue";
import handleErrorsMixin from "../mixins/handleErrorsMixin.js";
import api from "../api";
export default {
  name: "load-article",
  components: { SvgLoadingSpinner, ArticleInfo },
  mixins: [handleErrorsMixin], // data: error, methods: handleErrors, handleEmptyInputError

  data() {
    return {
      currentSearchedArticle: "", // инпут формы
      articleInfo: "", // блок результатов загрузки
      articles: [],
      loading: true, // загрузка статей в таблицу
      // процесс запроса статьи: watching, pending, success, failed
      fetchStatus: "waiting",
    };
  },

  computed: {
    isFormDisabled() {
      return this.fetchStatus === "pending";
    },
  },

  created() {
    api
      .get("articles")
      .then((res) => (this.articles = res.data))
      .catch((error) => (this.error = error.message))
      .finally(() => (this.loading = false));
  },

  methods: {
    loadArticle(e) {
      e.preventDefault();
      // return в случае ошибки
      this.handleEmptyInputError(this.currentSearchedArticle);

      this.fetchStatus = "pending";
      api
        .post("articles", { query: this.currentSearchedArticle })
        .then((response) => {
          this.fetchStatus = "success";
          this.articleInfo = response.data;
          this.articles = [response.data, ...this.articles];
          this.currentSearchedArticle = "";
        })
        .catch((err) => {
          this.fetchStatus = "failed";
          this.handleErrors(err);
        });
    },
  },
};
</script>

<style scoped>
.load-info {
  height: 150px;
  overflow: auto;
}
</style>