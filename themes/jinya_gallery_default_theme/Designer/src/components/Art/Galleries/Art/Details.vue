<template>
    <jinya-gallery-form :back-target="backRoute" :static="true" :gallery="gallery" @save="edit" :message="message"
                        :state="state" :hide-on-error="true" save-label="art.galleries.details.edit"/>
</template>

<script>
  import JinyaGalleryForm from "@/components/Art/Galleries/GalleryForm";
  import Routes from "@/router/Routes";
  import Translator from "@/framework/i18n/Translator";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import DOMUtils from "@/framework/Utils/DOMUtils";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaGalleryForm
    },
    name: "art-gallery-details",
    computed: {
      backRoute() {
        return Routes.Art.Galleries.Art.Overview;
      }
    },
    data() {
      return {
        message: '',
        state: '',
        gallery: {
          background: '',
          name: '',
          slug: '',
          description: ''
        },
        overviewRoute: Routes.Art.Galleries.Art.Overview.name
      };
    },
    async mounted() {
      this.state = 'loading';
      this.message = Translator.message('art.galleries.details.loading');
      try {
        const gallery = await JinyaRequest.get(`/api/gallery/art/${this.$route.params.slug}`);
        this.gallery = gallery.item;
        this.state = '';
        DOMUtils.changeTitle(this.gallery.name);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.galleries.${error.message}`);
      }
    },
    methods: {
      edit() {
        this.$router.push({
          name: Routes.Art.Galleries.Art.Edit.name,
          params: {
            slug: this.gallery.slug
          }
        });
      }
    }
  }
</script>