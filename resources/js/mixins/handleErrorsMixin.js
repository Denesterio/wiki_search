export default {
  data() {
    return {
      error: "",
    };
  },

  methods: {
    handleErrors(error) {
      if (error?.response?.data?.status === 404) {
        this.error = error.response.data.message;
      } else if (error?.response?.status === 422) {
        this.error = Object.entries(error.response.data.errors)[0][1][0];
      } else {
        this.error = "Ошибка сервера";
      }
    },

    handleEmptyInputError(input) {
      if (input.length === 0) {
        this.error = "Поле не должно быть пустым";
        this.fetchStatus = "failed";
        return;
      }
    },
  },
};
