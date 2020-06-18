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
    </div>
</template>

<script>
    import Dropzone from '@/components/Dropzone'

    export default {
        name: 'DropzoneDemo',
        components: {Dropzone},
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

