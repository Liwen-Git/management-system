<template>
    <div class="components-container">
        <aside>
            Based on <a class="link-type" href="https://github.com/rowanwins/vue-dropzone"> dropzone </a>.
            批量上传图片到七牛图库，代码详情请看 @/components/Dropzone.
        </aside>
        <div class="editor-container">
            <dropzone id="myVueDropzone" url="https://httpbin.org/post" @dropzone-removedFile="dropzoneR"
                      @dropzone-success="dropzoneS"/>
        </div>

        <aside style="margin-top: 15px;">图片上传组件</aside>
        <upload-image :preview="true" :multiple="true"></upload-image>

        <aside style="margin-top: 15px;">文件上传组件</aside>
        <upload-file :drag="false"></upload-file>
    </div>
</template>

<script>
    import Dropzone from '@/components/Dropzone'

    import UploadImage from '@/components/Upload/image.vue'
    import UploadFile from '@/components/Upload/file.vue'

    export default {
        name: 'DropzoneDemo',
        components: {
            Dropzone,
            UploadImage,
            UploadFile
        },
        methods: {
            dropzoneS(file) {
                console.log(file)
                this.$message({message: 'Upload success', type: 'success'})
            },
            dropzoneR(file) {
                console.log(file)
                this.$message({message: 'Delete success', type: 'success'})
            }
        }
    }
</script>

