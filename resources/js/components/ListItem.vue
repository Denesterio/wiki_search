<template>
  <li class="mb-3">
    <div class="row justify-content-between">
      <div class="col-sm-9">
        <h4 class="my-1 py-1">{{ article.title }}</h4>
        <p class="my-0 py-0 text-muted">совпадений: {{ article.count }}</p>
      </div>
      <div class="col-auto d-flex align-items-center">
        <button
          @click="toggleArticle"
          class="btn btn-primary list-item-btn"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapseArticle"
          aria-expanded="false"
          aria-controls="collapseArticle"
        >
          Показать
        </button>
      </div>
    </div>
    <div
      class="collapse w-100 border my-3"
      :class="{ show: articleCollapsed === false }"
      id="collapseArticle"
    >
      <article class="w-100 p-3">
        {{ articleBody }}
      </article>
    </div>
  </li>
</template>

<script>
import api from "../api";
export default {
  props: {
    // статья без тела, article_id вместо id
    article: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      articleCollapsed: true,
      articleBody: null,
      error: "",
    };
  },

  methods: {
    toggleArticle() {
      this.articleCollapsed = !this.articleCollapsed;
      if (!this.articleBody) {
        api
          .get("article", this.article.article_id)
          .then((response) => {
            this.articleBody = response.data.body;
          })
          .catch((error) => {
            console.dir(error);
            this.error = "Не удалось загрузить статью";
          });
      }
    },
  },
};
</script>

<style scoped>
#collapseArticle {
  max-height: 500px;
  overflow: auto;
}
article {
  white-space: pre-line;
}
</style>
