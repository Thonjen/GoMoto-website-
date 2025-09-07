<template>
  <div class="custom-uploader">
    <!-- Single Photo Uploader (for main photo) -->
    <div v-if="!multiple" class="single-uploader">
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
          <img :src="previewUrl" alt="Preview" class="preview-image" />
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
            <h4 class="text-lg font-semibold text-white mb-1">Main Vehicle Photo</h4>
            <p class="text-white/80 text-sm mb-1">Drag & drop or click to upload</p>
            <p class="text-white/60 text-xs">Supports: JPEG, PNG, WebP (max 5MB)</p>
          </div>
        </div>
      </div>

      <!-- File Input -->
      <input
        ref="fileInput"
        type="file"
        accept="image/jpeg,image/jpg,image/png,image/webp"
        @change="handleFileSelect"
        class="hidden"
      />
    </div>

    <!-- Multiple Photos Uploader -->
    <div v-else class="multiple-uploader">
      <div
        class="upload-area multiple"
        :class="{ 'drag-over': isDragOver }"
        @drop="handleDrop"
        @dragover.prevent="isDragOver = true"
        @dragleave="isDragOver = false"
        @click="triggerFileInput"
      >
        <div class="upload-prompt">
          <div class="upload-icon">
            <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <div class="upload-text">
            <h4 class="text-base font-semibold text-white mb-1">Upload Additional Photos</h4>
            <p class="text-white/80 text-sm mb-1">Drag & drop or click to select multiple photos</p>
            <p class="text-white/60 text-xs">Up to 8 photos, max 5MB each</p>
          </div>
        </div>
      </div>

      <!-- Selected Files Preview -->
      <div v-if="selectedFiles.length > 0" class="selected-files">
        <div class="files-header">
          <h5 class="text-white font-medium">Selected Photos ({{ selectedFiles.length }})</h5>
          <button @click="clearFiles" class="text-red-400 hover:text-red-300 text-sm">Clear All</button>
        </div>
        <div class="files-grid">
          <div v-for="(file, index) in selectedFiles" :key="index" class="file-item">
            <img :src="file.preview" alt="Preview" class="file-preview" />
            <div class="file-info">
              <p class="file-name">{{ file.name }}</p>
              <p class="file-size">{{ formatFileSize(file.size) }}</p>
            </div>
            <button @click="removeFile(index)" class="remove-file-btn">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Upload Button -->
        <button
          @click="uploadFiles"
          :disabled="uploading"
          class="upload-btn"
          :class="{ 'uploading': uploading }"
        >
          <svg v-if="uploading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ uploading ? 'Uploading...' : `Upload ${selectedFiles.length} Photo${selectedFiles.length !== 1 ? 's' : ''}` }}
        </button>
      </div>

      <!-- File Input -->
      <input
        ref="fileInput"
        type="file"
        accept="image/jpeg,image/jpg,image/png,image/webp"
        multiple
        @change="handleFileSelect"
        class="hidden"
      />
    </div>

    <!-- Upload Status -->
    <div v-if="message" class="upload-message" :class="messageType">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  multiple: {
    type: Boolean,
    default: false
  },
  vehicleId: {
    type: Number,
    default: null
  },
  maxFiles: {
    type: Number,
    default: 8
  },
  maxFileSize: {
    type: Number,
    default: 5 * 1024 * 1024 // 5MB in bytes
  }
})

const emit = defineEmits(['file-selected', 'files-uploaded'])

const fileInput = ref(null)
const isDragOver = ref(false)
const selectedFiles = ref([])
const previewUrl = ref(null)
const currentFile = ref(null)
const uploading = ref(false)
const message = ref('')
const messageType = ref('')

function triggerFileInput() {
  fileInput.value?.click()
}

function handleDrop(e) {
  e.preventDefault()
  isDragOver.value = false
  
  const files = Array.from(e.dataTransfer.files)
  processFiles(files)
}

function handleFileSelect(e) {
  const files = Array.from(e.target.files)
  processFiles(files)
}

function processFiles(files) {
  const validFiles = files.filter(file => {
    if (!file.type.startsWith('image/')) {
      showMessage('Only image files are allowed', 'error')
      return false
    }
    if (file.size > props.maxFileSize) {
      showMessage(`File ${file.name} is too large. Maximum size is ${formatFileSize(props.maxFileSize)}`, 'error')
      return false
    }
    return true
  })

  if (props.multiple) {
    // Multiple files mode
    const remainingSlots = props.maxFiles - selectedFiles.value.length
    const filesToAdd = validFiles.slice(0, remainingSlots)
    
    if (filesToAdd.length < validFiles.length) {
      showMessage(`Only ${filesToAdd.length} files added. Maximum is ${props.maxFiles} files.`, 'warning')
    }

    filesToAdd.forEach(file => {
      const reader = new FileReader()
      reader.onload = (e) => {
        selectedFiles.value.push({
          file: file,
          name: file.name,
          size: file.size,
          preview: e.target.result
        })
      }
      reader.readAsDataURL(file)
    })
  } else {
    // Single file mode
    if (validFiles.length > 0) {
      const file = validFiles[0]
      currentFile.value = file
      
      const reader = new FileReader()
      reader.onload = (e) => {
        previewUrl.value = e.target.result
      }
      reader.readAsDataURL(file)
      
      emit('file-selected', file)
    }
  }
  
  // Clear the input
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

function removeImage() {
  previewUrl.value = null
  currentFile.value = null
  emit('file-selected', null)
}

function removeFile(index) {
  selectedFiles.value.splice(index, 1)
}

function clearFiles() {
  selectedFiles.value = []
}

async function uploadFiles() {
  if (selectedFiles.value.length === 0 || !props.vehicleId) return
  
  uploading.value = true
  message.value = ''
  
  try {
    const formData = new FormData()
    selectedFiles.value.forEach(fileObj => {
      formData.append('photos[]', fileObj.file)
    })
    
    const response = await axios.post(`/owner/vehicles/${props.vehicleId}/photos`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    showMessage(`Successfully uploaded ${selectedFiles.value.length} photo(s)!`, 'success')
    emit('files-uploaded', response.data.photos)
    selectedFiles.value = []
    
  } catch (error) {
    console.error('Upload error:', error)
    showMessage(error.response?.data?.message || 'Upload failed. Please try again.', 'error')
  } finally {
    uploading.value = false
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
</script>

<style scoped>
.custom-uploader {
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

/* Single Uploader */
.single-uploader .upload-area {
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
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
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
}

.image-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  border-radius: 10px;
}

.image-preview:hover .image-overlay {
  background: rgba(0, 0, 0, 0.6);
}

.overlay-content {
  text-align: center;
  transform: translateY(10px);
  opacity: 0;
  transition: all 0.3s ease;
}

.image-preview:hover .overlay-content {
  transform: translateY(0);
  opacity: 1;
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

/* Multiple Uploader */
.multiple-uploader .upload-area.multiple {
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.multiple-uploader .upload-prompt {
  padding: 1.5rem;
}

/* Selected Files */
.selected-files {
  margin-top: 1rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  padding: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.files-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.files-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.file-item {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 6px;
  overflow: hidden;
  position: relative;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.file-preview {
  width: 100%;
  height: 80px;
  object-fit: cover;
}

.file-info {
  padding: 0.5rem;
}

.file-name {
  font-size: 0.75rem;
  color: white;
  font-weight: 500;
  truncate: true;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.file-size {
  font-size: 0.625rem;
  color: rgba(255, 255, 255, 0.6);
}

.remove-file-btn {
  position: absolute;
  top: 4px;
  right: 4px;
  background: rgba(239, 68, 68, 0.8);
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.remove-file-btn:hover {
  background: rgba(239, 68, 68, 1);
}

/* Upload Button */
.upload-btn {
  width: 100%;
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

.upload-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #1d4ed8, #1e40af);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.upload-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Upload Message */
.upload-message {
  margin-top: 0.75rem;
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

.upload-message.warning {
  background: rgba(245, 158, 11, 0.1);
  border: 1px solid rgba(245, 158, 11, 0.3);
  color: #f59e0b;
}

/* Responsive Design */
@media (max-width: 640px) {
  .files-grid {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.5rem;
  }
  
  .upload-prompt {
    padding: 1rem;
  }
  
  .upload-prompt h4 {
    font-size: 1rem;
  }
}

/* Animation */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.file-item {
  animation: fadeInUp 0.3s ease;
}
</style>
