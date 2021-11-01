<template>
  <table class="table">
    <thead class="table-primary">
      <tr>
        <th v-for="header in $options.HEADERS" :key="header">
          {{ $options.HEADERS_TRANSLATION[header] }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="article in articles" :key="article.id">
        <td v-for="header in $options.HEADERS" :key="header + article.id">
          {{ article[header] }}
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    articles: {
      type: Array,
      required: false,
      default: () => [],
    },
  },

  HEADERS: ["id", "title", "link", "words_count"],
  HEADERS_TRANSLATION: {
    id: "id",
    title: "Название",
    link: "Ссылка",
    words_count: "Всего слов",
  },
};
</script>