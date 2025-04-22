<template>
  <ClientOnly>
    <Editor
      v-model="modelValue"
      :init="{
        height: 700,
        menubar: true,
        plugins: [
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount'
        ],
        toolbar:
          'undo redo | formatselect | bold  italic backcolor | \
           alignleft aligncenter alignright alignjustify | \
           bullist numlist outdent indent | image | removeformat | help',
        language: 'vi',
        paste_data_images: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_handler: async (blobInfo : any, progress: any) => {
          try {
            const file = blobInfo.blob();

            // Validate file type
            const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validImageTypes.includes(file.type)) {
              throw new Error('Chỉ hỗ trợ các định dạng ảnh JPEG, PNG, GIF, hoặc WebP');
            }

            // Validate file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (file.size > maxSize) {
              throw new Error('Kích thước ảnh không được vượt quá 5MB');
            }

            progress(0);

            const {data} = await uploadImage(file);


            progress(100);
            return data.value?.location;
          } catch (error) {
            console.error('Image upload failed:', error);
            throw new Error('Tải ảnh lên thất bại. Vui lòng thử lại.');
          }
        }
      }"
      api-key="66rc9s8j5hnodqqc3etm26ywf4aon0m3haeycdbrres5p5k7"
    />
  </ClientOnly>
</template>

<script setup lang="ts">
import Editor from '@tinymce/tinymce-vue'
const modelValue = defineModel<string>({ required: true })
const { uploadImage } = usePosts()
const runtimeConfig = useRuntimeConfig()
</script>