<template>
  <div>
    <div class="container top-0 position-sticky z-index-sticky">
      <div class="row">
        <div class="col-12">
          <navbar
            is-blur="blur blur-rounded my-3 py-2 start-0 end-0 mx-4 shadow"
            btn-background="bg-gradient-success"
            :dark-mode="true"
          />
        </div>
      </div>
    </div>
    <main class="mt-0 main-content main-content-bg">
      <section>
        <div class="page-header min-vh-75">
          <div class="container">
            <div class="row">
              <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                <div class="card card-plain">
                  <div class="pb-0 card-header text-start text-center">
                    <!-- Provincial Logo -->
                    <div class="mb-3">
                      <img 
                        src="/images/PROVINCIAL OF AURORA.jpg" 
                        alt="Provincial of Aurora" 
                        style="height: 80px; width: auto; object-fit: contain;"
                      />
                    </div>
                    <h3 class="font-weight-bolder text-success text-gradient">
                      Welcome back
                    </h3>
                    <p class="mb-0">Enter your username and password to sign in</p>
                  </div>
                  <div class="card-body" style="background: white !important;">
                    <form role="form" class="text-start" @submit="handleSubmit">
                      <label>Username</label>
                      <soft-input
                        @input="form.username = $event.target.value"
                        id="username"
                        placeholder="Username"
                        name="username"
                      />
                      <label>Password</label>
                      <soft-input
                        @input="form.password = $event.target.value"
                        id="password"
                        type="password"
                        placeholder="Password"
                        name="password"
                      />
                      <soft-switch id="rememberMe" name="rememberMe" checked>
                        Remember me
                      </soft-switch>
                      <div class="text-center">
                        <soft-button
                          class="my-4 mb-2"
                          variant="gradient"
                          color="success"
                          full-width
                          >Login
                        </soft-button>
                      </div>
                    </form>
                  </div>
                  <div class="px-1 pt-0 card-footer px-lg-2" style="background: white !important; padding-left: 25px !important; ">
                    <p class="mx-auto mb-4 text-sm">
                      Do you have a assessment of you land property? <br/>
                      <router-link
                        :to="{ name: 'Track Reference' }"
                        class="text-success text-gradient font-weight-bold"
                        >Track Here</router-link
                      >
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n4">
                  <div
                    class="oblique-image bg-cover position-absolute fixed-top h-100 z-index-0"
                    :style="{
                      backgroundImage: 'url(/images/Aurora-Capitol.jpg)',
                      left: '-20%'
                    }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import Navbar from "@/examples/PageLayout/Navbar.vue";
// import AppFooter from "@/examples/PageLayout/Footer.vue";
import SoftInput from "@/components/SoftInput.vue";
import SoftSwitch from "@/components/SoftSwitch.vue";
import SoftButton from "@/components/SoftButton.vue";
import AuthService from "@/services/auth.js";
const body = document.getElementsByTagName("body")[0];
import { mapMutations } from "vuex";

export default {
  name: "SignIn",
  data() {
    return {
      form: {
        username: "",
        password: "",
      },
    };
  },
  components: {
    Navbar,
    // AppFooter,
    SoftInput,
    SoftSwitch,
    SoftButton,
  },
  created() {
    this.toggleEveryDisplay();
    this.toggleHideConfig();
    body.classList.remove("bg-gray-100");
  },
  beforeUnmount() {
    this.toggleEveryDisplay();
    this.toggleHideConfig();
    body.classList.add("bg-gray-100");
  },
  methods: {
    ...mapMutations(["toggleEveryDisplay", "toggleHideConfig"]),
    async handleSubmit(e) {
      e.preventDefault();
      console.log("Form data: ", this.form);
      
      try {
        // Use the enhanced login method from AuthService
        const loginResult = await AuthService.loginWithRole(this.form.username, this.form.password);
        
        if (loginResult.success) {
          console.log("Login successful: ", loginResult.user);
          
          // Redirect based on user role
          if (loginResult.user.role === 'admin') {
            this.$router.push("/admin");
          } else {
            this.$router.push("/dashboard");
          }
        } else {
          console.error("Login failed: ", loginResult.error);
          // Show error message
          if (this.$message && this.$message.error) {
            this.$message.error(loginResult.error);
          } else {
            alert(loginResult.error); // Fallback
          }
        }
      } catch (err) {
        console.error("Login error: ", err);
        if (this.$message && this.$message.error) {
          this.$message.error("An error occurred during login");
        } else {
          alert("An error occurred during login"); // Fallback
        }
      }
    },
  },
};
</script>
