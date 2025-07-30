<template>
  <div class="my-4">
    <file-pond
      ref="pond"
      v-model="files"
      :label-idle="labelIdle"
      :accepted-file-types="acceptedFileTypes"
      :allow-multiple="maxFiles > 1"
      :max-files="maxFiles"
      :instant-upload="true"
      :image-resize-target-width="800"
      :image-resize-mode="'contain'"
      :image-transform-output-quality="0.5"
      :image-transform-output-mime-type="'image/jpeg'"
      :max-file-size="'2MB'"
      @updatefiles="updateFiles"
      :server="serverConfig"
    />
    <div v-if="isUploading" class="mt-2 text-gray-700">
      <span>Uploading: {{ uploadProgress }}%</span>
      <div class="w-full bg-gray-200 rounded h-2 mt-1">
        <div
          class="bg-blue-600 h-2 rounded"
          :style="{ width: uploadProgress + '%' }"
        ></div>
      </div>
    </div>
    <div v-if="existingFileUrl && !files.length" class="mt-2">
      <img :src="existingFileUrl" alt="Existing file" class="w-32 h-32 object-contain border rounded mx-auto" />
    </div>
    <div v-if="errorMessage" class="mt-2 text-red-600">
      <span>Error: {{ errorMessage }}</span>
    </div>
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

import { ref, computed, defineExpose } from 'vue';
import axios from 'axios';

// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const props = defineProps({
  maxFiles: { type: Number, default: 5 },
  acceptedFileTypes: { type: Array, default: () => ['image/jpeg', 'image/png'] },
  uploadUrl: { type: String, required: true },
  existingFileUrl: { type: String, default: '' },
  labelIdle: { type: String, default: "Drag & Drop your file or <span class='filepond--label-action'>Browse</span>" }
});
const emit = defineEmits(['upload-success']);

const FilePond = vueFilePond(
  FilePondPluginImagePreview,
  FilePondPluginFileValidateType,
  FilePondPluginImageResize,
  FilePondPluginImageTransform,
  FilePondPluginFileValidateSize
);

const pond = ref(null);
const files = ref([]);
const isUploading = ref(false);
const uploadProgress = ref(0);
const errorMessage = ref('');

// FilePond server config for upload, revert, and load
const serverConfig = {
  process: (fieldName, file, metadata, load, error, progress, abort) => {
    isUploading.value = true;
    uploadProgress.value = 0;
    errorMessage.value = '';

    const formData = new FormData();
    formData.append('gcash_qr', file);

    axios.post(props.uploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
      },
      withCredentials: true,
      onUploadProgress: e => {
        if (e.lengthComputable) {
          uploadProgress.value = Math.round((e.loaded * 100) / e.total);
          progress(true, e.loaded, e.total);
        }
      }
    })
      .then(response => {
        isUploading.value = false;
        uploadProgress.value = 100;
        emit('upload-success', response.data);
        load(response.data.gcashQrUrl || 'success');
      })
      .catch(err => {
        isUploading.value = false;
        uploadProgress.value = 0;
        errorMessage.value = err.response?.data?.message || 'Upload failed';
        error(errorMessage.value);
      });

    return {
      abort: () => {
        isUploading.value = false;
        errorMessage.value = 'Upload aborted';
        abort();
      }
    };
  },
revert: (uniqueFileId, load, error) => {
  axios.delete(props.uploadUrl, {
    headers: {
      ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
      
            'X-Inertia': false,


    },
    withCredentials: true,
  })
    .then(() => {
      load();
    })
    .catch((err) => {
      error('Failed to delete file');
    });
}

};

function updateFiles(newFiles) {
  files.value = newFiles;
}

function clearFiles() {
  pond.value?.removeFiles();
}

defineExpose({ clearFiles });

</script>