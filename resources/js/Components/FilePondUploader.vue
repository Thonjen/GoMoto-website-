<template>
  <div class="my-4">
    <file-pond
      name="main_photo"
      ref="pond"
      label-idle="Drag & Drop your vehicle photo or <span class='filepond--label-action'>Browse</span>"
      :accepted-file-types="['image/jpeg', 'image/png', 'image/webp', 'image/gif']"
      :allow-multiple="false"
      :max-files="1"
      :instant-upload="false"
      :image-resize-target-width="1200"
      :image-resize-target-height="'800'"
      :image-resize-mode="'contain'"
      :image-transform-output-quality="0.8"
      :image-transform-output-mime-type="'image/jpeg'"
      :max-file-size="'5MB'"
      @updatefiles="updateFiles"
    />
  </div>
</template>

<script setup>
import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';

import { ref } from 'vue';

const emit = defineEmits(['file-added']);

// Register plugins
const FilePond = vueFilePond(
  FilePondPluginImagePreview,
  FilePondPluginFileValidateType,
  FilePondPluginImageResize,
  FilePondPluginImageTransform,
  FilePondPluginFileValidateSize
);

const pond = ref(null);

function updateFiles(newFiles) {
  if (newFiles.length > 0) {
    emit('file-added', newFiles[0].file);
  } else {
    emit('file-added', null);
  }
}
</script>
