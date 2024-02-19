<template>
   <div>
      <navbar></navbar>
      <side-bar></side-bar>
      <div id="main" class="mt-12 xl:mt-20 md:pl-[3.35rem] mb-6">
         <router-view></router-view>
      </div>
   </div>
</template>

<script>
   import Modal from "@components/Modal.vue";

   export default {
      name: "Main",
      components: {Modal},
      data() {
         return {
            order_id: '',
            show_modal: false,
            imgModal: '',
            showImageModal: false,
         };
      },
      methods: {

      },
      created() {
         this.$store.state.user = this.$attrs?.user;
      }
   }
</script>