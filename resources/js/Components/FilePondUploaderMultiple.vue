<template>
  <div class="my-4">
    <file-pond
      name="photos"
      ref="pond"
      label-idle="Drag & Drop your vehicle photos or <span class='filepond--label-action'>Browse</span>"
      accepted-file-types="image/jpeg, image/png"
      allow-multiple="true"
      max-files="5"
      instant-upload="false"
      :image-resize-target-width="800"
      :image-resize-mode="'contain'"
      :image-transform-output-quality="0.5" 
      :image-transform-output-mime-type="'image/jpeg'"
      :max-file-size="'2MB'"
      @updatefiles="updateFiles"
    />
    <button
      @click="upload"
      class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
    >
      Upload Photos
    </button>
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
import axios from 'axios';

const props = defineProps({ vehicleId: Number });

// Register plugins (resize + transform for compression)
const FilePond = vueFilePond(
  FilePondPluginImagePreview,
  FilePondPluginFileValidateType,
  FilePondPluginImageResize,
  FilePondPluginImageTransform,
  FilePondPluginFileValidateSize
);

const pond = ref(null);
const files = ref([]);

function updateFiles(newFiles) {
  files.value = newFiles.map(f => f.file);
}

async function upload() {
  if (!files.value.length) return;

  const formData = new FormData();
  files.value.forEach(file => formData.append('photos[]', file));

  await axios.post(`/owner/vehicles/${props.vehicleId}/photos`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });

  pond.value.removeFiles();
  window.location.reload(); // Refresh to show uploaded photos
}
</script>
