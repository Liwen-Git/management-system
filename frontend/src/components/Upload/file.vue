<template>
    <div>
        <el-upload
                action="action"
                class="file-uploader"
                :http-request="uploadFile"
                :file-list="fileList"
                :list-type="listType"
                :limit="limit"
                :disabled="disabled"
                :accept="accept"
                :drag="drag"
                :multiple="multiple"
                :on-success="handleUploadSuccess"
                :on-error="handleUploadError"
                :before-upload="beforeUpload"
                :on-remove="handleRemove"
                :on-exceed="onExceed"
        >
            <el-button size="small" type="primary" v-if="!drag">点击上传</el-button>

            <i class="el-icon-upload" v-if="drag"></i>
            <div class="el-upload__text" v-if="drag">将文件拖到此处，或<em>点击上传</em></div>

            <div slot="tip" class="el-upload__tip">{{tips}}</div>
        </el-upload>
    </div>
</template>

<script>
    import emitter from 'element-ui/src/mixins/emitter';
    /**
     * 文件上传组件
     *  选项：
     *      value: 绑定的文件地址
     *      valueType: 返回的数据类型，默认array
     *      uploadUrl: 自定义上传方法的上传地址
     *      listType: 文件列表类型，默认text
     *      limit: 最大允许上传文件数量
     *      disable: 是否禁用
     *      data: 上传时附带的额外参数
     *      accept: 介绍上传的文件类型
     *      drag: 是否启用拖拽上传，默认false
     *      multiple: 是否支持多文件上传
     *      limitSize: 限制文件大小，默认0不限制，单位M
     *
     *  事件:
     *      before: 文件上传前, 如果上传前的前置条件检查不通过, 则不会触发, 只有在请求上传服务器时才触发
     *      success: 文件上传成功
     *      fail: 文件上传失败
     */
    export default {
        name: 'FileUpload',
        props: {
            // eslint-disable-next-line vue/require-prop-type-constructor
            value: {type: Array | String},
            valueType: {type: String, default: 'array'},
            uploadUrl: {type: String, default: '/upload/file'},
            listType: {type: String, default: 'text'},
            limit: {type: Number},
            disabled: {type: Boolean, default: false},
            // eslint-disable-next-line vue/require-valid-default-prop
            data: {type: Object, default: () => {
                    return {dir: 'file'}
                }},
            accept: {type: String},
            drag: {type: Boolean, default: false},
            multiple: {type: Boolean, default: false},
            limitSize: {type: Number, default: 0},
            tips: {type: String, default: ''}
        },
        data() {
            return {
                fileList: []
            }
        },
        mixins: [emitter],
        methods: {
            emitInput() {
                let value = [];

                this.fileList.forEach(item => {
                    value.push(item.url)
                });
                if (this.valueType === 'string') {
                    value = value.join(',');
                }

                this.$emit('input', value);
                this.dispatch('ElFormItem', 'el.form.blur', [value]);
                this.dispatch('ElFormItem', 'el.form.change', [value]);
            },
            handleRemove(file, fileList) {
                this.fileList = fileList;
                this.emitInput()
            },
            uploadFile(param) {
                const formData = new FormData();
                formData.append('file', param.file);
                Object.keys(this.data).forEach(key => {
                    formData.append(key, this.data[key]);
                });
                this.axiosPost(this.uploadUrl, formData).then(res => {
                    param.onSuccess(res)
                }).catch(err => {
                    param.onError(err)
                })
            },
            handleUploadSuccess(res, file, fileList) {
                file.url = res.url;
                this.fileList = fileList;
                this.emitInput();
                this.$emit('success');
            },
            handleUploadError (err, file, fileList) {
                fileList.forEach(function (item, index) {
                    if (item === file) {
                        fileList.splice(index, 1)
                    }
                });
                this.$message.error('文件上传失败：' + err.message);
                this.$emit('fail')
            },
            beforeUpload(file) {
                const size = file.size;
                if (this.limitSize && size > this.limitSize * 1024 * 1024) {
                    this.$message.error('上传的文件不能大于' + this.limitSize + 'M');
                    return false;
                }
                this.$emit('before')
            },
            onExceed() {
                this.$message.warning(`最多只能上传${this.limit}个文件`)
            }
        }
    }
</script>

<style scoped>
    .file-uploader {
        display: inline-block;
        outline: none;
    }
</style>
