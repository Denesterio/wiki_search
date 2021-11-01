<template>
  <div class="container">
    <!-- навигация, табы -->
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <span
          @click="changeTab"
          class="nav-link"
          :class="{ active: currentComponent === 'LoadArticle' }"
          aria-current="page"
          >Загрузить</span
        >
      </li>
      <li class="nav-item">
        <span
          @click="changeTab"
          class="nav-link"
          :class="{ active: currentComponent === 'FindArticle' }"
          >Искать</span
        >
      </li>
    </ul>
    <!-- компонент с контентом -->
    <keep-alive :include="['load-article', 'find-article']">
      <component
        :is="currentComponent"
        class="border border-top-0 p-4"
      ></component>
    </keep-alive>
  </div>
</template>

<script>
import LoadArticle from "./LoadArticle.vue";
import FindArticle from "./FindArticle.vue";
export default {
  components: { LoadArticle, FindArticle },
  data() {
    return {
      currentComponent: "LoadArticle",
    };
  },

  methods: {
    changeTab() {
      this.currentComponent =
        this.currentComponent === "LoadArticle" ? "FindArticle" : "LoadArticle";
    },
  },
};
</script>

<style scoped>
.nav-link:hover {
  cursor: pointer;
}
</style>
