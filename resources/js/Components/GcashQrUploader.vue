<template>
  <div class="gcash-qr-uploader">
    <!-- Upload Area -->
    <div
      class="upload-area"
      :class="{ 'drag-over': isDragOver, 'has-image': previewUrl }"
      @drop="handleDrop"
      @dragover.prevent="isDragOver = true"
      @dragleave="isDragOver = false"
      @click="triggerFileInput"
    >
      <!-- Preview Image -->
      <div v-if="previewUrl" class="image-preview">
        <img :src="previewUrl" alt="GCash QR Preview" class="preview-image" />
        <div class="image-overlay">
          <div class="overlay-content">
            <svg class="w-8 h-8 text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-white text-sm font-medium">Click or drag to replace</p>
          </div>
        </div>
        <button
          @click.stop="removeImage"
          class="remove-button"
          title="Remove image"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Upload Prompt -->
      <div v-else class="upload-prompt">
        <div class="upload-icon">
          <svg class="w-12 h-12 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>
        <div class="upload-text">
          <h4 class="text-lg font-semibold text-white mb-1">GCash QR Code</h4>
          <p class="text-white/80 text-sm mb-1">Drag & drop or click to upload</p>
          <p class="text-white/60 text-xs">Supports: PNG, JPEG (max 5MB, high quality preserved)</p>
        </div>
      </div>
    </div>

    <!-- Upload Button -->
    <button
      v-if="selectedFile && !uploading"
      @click="uploadFile"
      class="upload-btn w-full mt-4"
    >
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
      </svg>
      Upload QR Code
    </button>

    <!-- Uploading State -->
    <div v-if="uploading" class="upload-progress mt-4">
      <div class="flex items-center justify-center text-white/80 mb-2">
        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Uploading QR Code...
      </div>
      <div class="w-full bg-white/20 rounded-full h-2">
        <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
      </div>
    </div>

    <!-- File Input -->
    <input
      ref="fileInput"
      type="file"
      accept="image/png,image/jpeg,image/jpg"
      @change="handleFileSelect"
      class="hidden"
    />

    <!-- Upload Status -->
    <div v-if="message" class="upload-message mt-4" :class="messageType">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({
  uploadUrl: {
    type: String,
    required: true
  },
  existingPhotos: {
    type: Array,
    default: () => []
  },
  maxFileSize: {
    type: Number,
    default: 5 * 1024 * 1024 // 5MB in bytes
  }
})

const emit = defineEmits(['upload-success', 'upload-error'])

const fileInput = ref(null)
const isDragOver = ref(false)
const previewUrl = ref(null)
const selectedFile = ref(null)
const uploading = ref(false)
const uploadProgress = ref(0)
const message = ref('')
const messageType = ref('')

// Set existing photo as preview if available
if (props.existingPhotos.length > 0) {
  previewUrl.value = props.existingPhotos[0]
}

function triggerFileInput() {
  fileInput.value?.click()
}

function handleDrop(e) {
  e.preventDefault()
  isDragOver.value = false
  
  const files = Array.from(e.dataTransfer.files)
  if (files.length > 0) {
    processFile(files[0])
  }
}

function handleFileSelect(e) {
  const files = Array.from(e.target.files)
  if (files.length > 0) {
    processFile(files[0])
  }
}

function processFile(file) {
  // Validate file type
  if (!file.type.match(/^image\/(png|jpeg|jpg)$/)) {
    showMessage('Please select a PNG or JPEG image file.', 'error')
    return
  }

  // Validate file size
  if (file.size > props.maxFileSize) {
    showMessage(`File is too large. Maximum size is ${formatFileSize(props.maxFileSize)}.`, 'error')
    return
  }

  selectedFile.value = file
  
  // Create preview without compression
  const reader = new FileReader()
  reader.onload = (e) => {
    previewUrl.value = e.target.result
  }
  reader.readAsDataURL(file)
  
  // Clear any previous messages
  message.value = ''
  
  // Clear the input
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

function removeImage() {
  previewUrl.value = null
  selectedFile.value = null
  message.value = ''
}

async function uploadFile() {
  if (!selectedFile.value || uploading.value) return

  uploading.value = true
  uploadProgress.value = 0
  message.value = ''

  try {
    const formData = new FormData()
    formData.append('gcash_qr', selectedFile.value)

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (csrfToken) {
      formData.append('_token', csrfToken)
    }

    const response = await axios.post(props.uploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'X-CSRF-TOKEN': csrfToken
      },
      onUploadProgress: (progressEvent) => {
        if (progressEvent.lengthComputable) {
          uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        }
      }
    })

    showMessage('GCash QR code uploaded successfully!', 'success')
    emit('upload-success', response.data)
    
    // Clear selected file after successful upload
    selectedFile.value = null

  } catch (error) {
    console.error('Upload error:', error)
    const errorMessage = error.response?.data?.message || 'Upload failed. Please try again.'
    showMessage(errorMessage, 'error')
    emit('upload-error', error)
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

function showMessage(text, type) {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 5000)
}

// Expose methods for parent components
defineExpose({
  clearFiles: removeImage
})
</script>

<style scoped>
.gcash-qr-uploader {
  width: 100%;
}

/* Upload Area Styles */
.upload-area {
  border: 2px dashed rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-area:hover {
  border-color: rgba(59, 130, 246, 0.5);
  background: rgba(59, 130, 246, 0.1);
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.upload-area.drag-over {
  border-color: #3b82f6;
  background: rgba(59, 130, 246, 0.15);
  transform: scale(1.02);
}

.upload-prompt {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  text-align: center;
  padding: 2rem;
}

.upload-icon {
  opacity: 0.7;
  transition: all 0.3s ease;
}

.upload-area:hover .upload-icon {
  opacity: 1;
  transform: scale(1.1);
}

/* Image Preview */
.image-preview {
  position: relative;
  width: 100%;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  border-radius: 8px;
}

.image-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  opacity: 0;
  transition: opacity 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.upload-area:hover .image-overlay {
  opacity: 1;
}

.overlay-content {
  text-align: center;
}

.remove-button {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(239, 68, 68, 0.8);
  color: white;
  border: none;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.remove-button:hover {
  background: rgba(239, 68, 68, 1);
  transform: scale(1.1);
}

/* Upload Button */
.upload-btn {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-btn:hover {
  background: linear-gradient(135deg, #1d4ed8, #1e40af);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

/* Upload Progress */
.upload-progress {
  text-align: center;
}

/* Upload Message */
.upload-message {
  padding: 0.75rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
}

.upload-message.success {
  background: rgba(34, 197, 94, 0.1);
  border: 1px solid rgba(34, 197, 94, 0.3);
  color: #22c55e;
}

.upload-message.error {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #ef4444;
}
</style>
