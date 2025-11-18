<template>
  <nav aria-label="breadcrumb">
    <ol
      class="px-0 pt-1 pb-0 mb-0 bg-transparent breadcrumb"
      :class="this.$store.state.isRTL ? '' : ' me-sm-6'"
    >
      <li class="text-sm breadcrumb-item text-white">
        <a
          v-if="this.$store.state.isRTL"
          class="opacity-5 ps-2 text-white"
          href="#"
          >لوحات القيادة</a
        >
        <a v-else class="opacity-8 text-white" href="#">Pages</a>
      </li>
      <li
        class="text-sm breadcrumb-item active text-white"
        aria-current="page"
      >
        {{ currentPage }}
      </li>
    </ol>
    <h6 class="mb-0 font-weight-bolder text-white">
      {{ currentPage }}
    </h6>
  </nav>
</template>

<script>
export default {
  name: "breadcrumbs",
  props: {
    currentPage: {
      required: true,
    },
    textWhite: {
      type: String,
    },
  },
};
</script>
