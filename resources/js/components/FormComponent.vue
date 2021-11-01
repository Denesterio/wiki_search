<template>
  <form class="row justify-content-between px-4">
    <input
      v-bind:value="value"
      @input="updateValue"
      type="text"
      class="col-10 form-control"
      :readonly="disabled"
    />
    <input
      type="submit"
      class="col-auto btn btn-primary"
      :disabled="disabled"
      :value="buttonLabel"
    />
  </form>
</template>

<script>
export default {
  props: {
    buttonLabel: {
      type: String,
      required: false,
      default: "Загрузить",
    },

    value: {
      type: String,
      required: false,
      default: "",
    },

    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  methods: {
    updateValue(event) {
      this.$emit("input", event.target.value);
    },
  },
};
</script>