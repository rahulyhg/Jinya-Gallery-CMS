<template>
    <jinya-artwork-form @save="save" :enable="enable" :message="message" :state="state"/>
</template>

<script>
  import JinyaArtworkForm from "@/components/Art/Artworks/SavedInJinya/ArtworkForm";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Translator from "@/framework/i18n/Translator";
  import Routes from "@/router/Routes";
  import Timing from "@/framework/Utils/Timing";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaArtworkForm
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: true
      }
    },
    name: "add",
    methods: {
      async save(artwork) {
        const picture = artwork.picture;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.artworks.add.saving', {name: artwork.name});

          await JinyaRequest.post('/api/artwork', {
            name: artwork.name,
            slug: artwork.slug,
            description: artwork.description
          });

          this.message = Translator.message('art.artworks.add.uploading', {name: artwork.name});
          await JinyaRequest.upload(`/api/artwork/${artwork.slug}/picture`, picture);

          this.state = 'success';
          this.message = Translator.message('art.artworks.add.success', {name: artwork.name});

          await Timing.wait();
          this.$router.push(Routes.Art.Artworks.SavedInJinya.Overview);
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      }
    }
  }
</script>
