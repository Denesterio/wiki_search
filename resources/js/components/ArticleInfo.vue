<template>
  <div>
    <p class="text-success">Страница успешно загружена</p>
    <p><b>Адрес:</b> {{ article.link }}</p>
    <p><b>Название:</b> {{ article.title }}</p>
    <p><b>Размер:</b> {{ article.length }} кб.</p>
    <p><b>Количество слов:</b> {{ article["words_count"] }}</p>
  </div>
</template>

<script>
export default {
  props: {
    article: {
      type: Object,
      required: false,
      default: () => {},
    },
  },
};
</script>