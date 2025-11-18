<template>
  <aside
    class="my-3 overflow-auto border-0 sidenav navbar navbar-vertical navbar-expand-xs border-radius-xl"
    :class="isRTL ? 'me-3 rotate-caret' : 'ms-3'"
    id="sidenav-main"
  >
    <div class="sidenav-header">
      <i
        class="top-0 p-3 cursor-pointer fas fa-times text-secondary opacity-5 position-absolute end-0 d-none d-xl-none"
        aria-hidden="true"
        id="iconSidenav"
      ></i>
      <a class="navbar-brand d-flex justify-content-center align-items-center" href="/" style="height: 180px; padding: 25px;">
        <img :src="logo" class="logo-img" alt="main_logo" />
        <span class="ms-1 font-weight-bold"></span>
      </a>
    </div>
    <hr class="horizontal dark" style="margin-top: 30px; margin-bottom: 30px;" />
    <div class="sidenav-content" style="margin-top: 40px; padding-top: 20px;">
      <sidenav-list :cardBg="customClass" />
    </div>
  </aside>
</template>
<script>
import SidenavList from "./SidenavList.vue";
import { mapState } from "vuex";

export default {
  name: "index",
  components: {
    SidenavList,
  },
  data() {
    return {
      logo: '/images/PROVINCIAL OF AURORA.jpg',
    };
  },
  props: {
    customClass: {
      type: String,
      default: "",
    },
  },
  computed: {
    ...mapState(["isRTL"]),
  },
};
</script>

<style scoped>
.logo-img {
  max-width: 140px;
  max-height: 140px;
  min-width: 120px;
  min-height: 120px;
  width: 130px;
  height: auto;
  object-fit: contain;
  border-radius: 8px;
}

.navbar-brand {
  padding: 25px !important;
  margin: 0 !important;
  width: 100%;
}

.sidenav-header {
  text-align: center;
  padding: 15px 0 30px 0;
  margin-bottom: 20px;
}

/* Add more spacing for the menu items container */
.sidenav-list {
  margin-top: 30px;
  padding-top: 20px;
}

/* Ensure proper spacing for the entire sidebar content */
.sidenav {
  padding-bottom: 20px;
}

/* Override any global styles that might be causing overlap */
:deep(.nav-item) {
  margin-top: 5px;
}

:deep(.sidenav-list) {
  margin-top: 30px !important;
}
</style>
