<template>
<div class="my-4">
    <file-pond
      name="photos"
      ref="pond"
      label-idle="Drag & Drop your vehicle photos or <span class='filepond--label-action'>Browse</span>"
      :accepted-file-types="['image/jpeg', 'image/png', 'image/webp', 'image/gif']"
      :allow-multiple="true"
      :max-files="8"
      :instant-upload="false"
      :image-resize-target-width="1200"
      :image-resize-target-height="800"
      :image-resize-mode="'contain'"
      :image-transform-output-quality="0.8"
      :image-transform-output-mime-type="'image/jpeg'"
      :max-file-size="'5MB'"
      @updatefiles="updateFiles"
    />
    
    <!-- Success/Error Message -->
    <div v-if="message" class="mt-2 p-3 rounded-md text-sm" :class="{
      'bg-green-100 border border-green-300 text-green-700': messageType === 'success',
      'bg-red-100 border border-red-300 text-red-700': messageType === 'error'
    }">
      {{ message }}
    </div>
    
    <button
      @click="upload"
      :disabled="!files.length || uploading"
      class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center"
    >
      <svg v-if="uploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      {{ uploading ? 'Uploading...' : `Upload Photos (${files.length})` }}
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
const emit = defineEmits(['photos-uploaded']);

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
const uploading = ref(false);
const message = ref('');
const messageType = ref('');

function updateFiles(newFiles) {
  files.value = newFiles.map(f => f.file);
  // Clear any previous messages when files change
  message.value = '';
}

async function upload() {
  if (!files.value.length || uploading.value) return;

  try {
    uploading.value = true;
    message.value = '';
    
    const formData = new FormData();
    files.value.forEach(file => formData.append('photos[]', file));

    const response = await axios.post(`/owner/vehicles/${props.vehicleId}/photos`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    // Clear the files from FilePond
    pond.value.removeFiles();
    files.value = [];

    // Show success message
    message.value = `Successfully uploaded ${response.data.photos.length} photo(s)!`;
    messageType.value = 'success';

    // Emit event to parent component
    emit('photos-uploaded', response.data.photos);

    // Clear success message after 3 seconds
    setTimeout(() => {
      message.value = '';
    }, 3000);

  } catch (error) {
    console.error('Error uploading photos:', error);
    message.value = error.response?.data?.message || 'Error uploading photos. Please try again.';
    messageType.value = 'error';
  } finally {
    uploading.value = false;
  }
}
</script>

<style scoped>
/* Ensure FilePond doesn't interfere with other elements */
:deep(.filepond--root) {
  position: relative;
  z-index: 1;
}

/* Ensure upload button is clickable */
button {
  position: relative;
  z-index: 2;
}

/* Override any conflicting FilePond z-index */
:deep(.filepond--panel-root) {
  z-index: 1 !important;
}

:deep(.filepond--image-preview-wrapper) {
  z-index: 1 !important;
}
</style>
