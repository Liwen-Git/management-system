<template>
    <div>
        <el-upload
                class="uploader"
                action="action"
                :http-request="uploadImage"
                :list-type="listType"
                :file-list="fileList"
                :on-preview="preview ? handlePreview : null"
                :on-success="handleUploadSuccess"
                :on-error="handleUploadError"
                :before-upload="beforeUpload"
                :on-remove="handleRemove"
                :disabled="disabled"
                :limit="limit"
                :data="data"
                :multiple="multiple"
                :on-exceed="onExceed"
                :class="{'upload-fulled' : fileList.length >= limit}"
        >
            <i v-if="!$slots.default" class="el-icon-plus"></i>
            <slot/>
        </el-upload>
        <el-dialog :visible.sync="isShow">
            <img width="100%" :src="previewImage" alt="预览图片">
        </el-dialog>
    </div>
</template>

<script>
    import emitter from 'element-ui/src/mixins/emitter';
    import {getToken} from '@/utils/auth';
    /**
     * 多图片上传组件
     * 已完成功能:
     *  选项:
     *      value: 绑定的图片地址
     *      action: 图片上传服务器地址, 默认为: '/pub/upload/index'
     *      width: 图片宽度
     *      height: 图片高度
     *      checkSize: 是否检查图片宽高 (宽高都传入时才有效)
     *      limit: 限制高度
     *      disabled: 是否可删除, 禁用
     *      listType: 图片列表类型: picture-card/picture/text, 默认: picture-card
     *      preview: 是否可预览图片
     *      multiple: 是否支持多文件上传
     *      valueType: 返回图片数据类型 默认：array
     *      uploadUrl: 自定义上传方法的上传地址
     *  功能:
     *      图片上传功能
     *      删除按钮
     *  事件:
     *      before: 图片上传前, 如果上传前的前置条件检查不通过, 则不会触发, 只有在请求上传服务器时才触发
     *      success: 图片上传成功
     *      fail: 图片上传失败
     *      complete: 上传后(不区分成功与失败, 都会执行)
     */
    export default {
        name: 'ImageUpload',
        props: {
            // eslint-disable-next-line vue/require-prop-type-constructor
            value: {type: Array | String},
            action: {type: String, default: process.env.VUE_APP_BASE_API + '/upload/image'},
            width: {type: Number},
            height: {type: Number},
            checkSize: {type: Boolean, default: true},
            limit: {type: Number},
            disabled: {type: Boolean, default: false},
            listType: {type: String, default: 'picture-card'},
            preview: {type: Boolean, default: false},
            data: {type: Object, default: () => {}},
            multiple: {type: Boolean, default: false},
            valueType: {type: String, default: 'array'},
            uploadUrl: {type: String, default: '/upload/image'}
        },
        mixins: [emitter],
        data() {
            return {
                headerObj: {Authorization: getToken()},
                fileList: [],
                isShow: false,
                previewImage: ''
            }
        },
        computed: {
            imageHeight() {
                return this.height && this.width ? (this.height / this.width * 180) + 'px' : 'auto';
            },
            iconHeight() {
                return this.height && this.width ? (this.height / this.width * 180) + 'px' : '180px';
            },
            iconLineHeight() {
                let lineHeight = this.height && this.width ? (this.height / this.width * 180 - 2) : 178;
                if (lineHeight < 28) {
                    lineHeight = 28;
                }
                return lineHeight + 'px';
            }
        },
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
            handlePreview(file) {
                this.previewImage = file.url;
                this.isShow = true;
            },
            handleRemove(file, fileList) {
                this.fileList = fileList;
                this.emitInput()
            },
            uploadImage(param){
                const formData = new FormData();
                formData.append('file', param.file);
                this.axiosPost(this.uploadUrl, formData).then(res => {
                    param.onSuccess(res)
                }).catch(err => {
                    param.onError(err)
                })
            },
            handleUploadSuccess(res, file, fileList) {
                const width = this.width;
                const height = this.height;
                if (
                    (!width || width <= 0 || parseInt(res.width) === width) &&
                    (!height || height <= 0 || parseInt(res.height) === height)
                ) {
                    file.url = res.url;
                    this.fileList = fileList;
                    this.emitInput();
                } else {
                    fileList.forEach(function (item, index) {
                        if (item === file) {
                            fileList.splice(index, 1)
                        }
                    });
                    this.$message.error('请上传图片尺寸为' + width + 'px*' + height + 'px且大小不能超过2MB的图片')
                    return false;
                }
                this.$emit('success')
                this.$emit('complete')
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
                const imgTypes = ['image/png', 'image/jpeg', 'image/gif'];
                const size = file.size;
                if (imgTypes.indexOf(file.type) < 0) {
                    this.$message.error('只能上传 png、jpg、jpeg或gif类型的图片');
                    return false;
                }
                if (size > 2 * 1024 * 1024) {
                    this.$message.error('上传的图片不能大于2M');
                    return false;
                }
                this.$emit('before')
            },
            onExceed() {
                this.$message.warning(`最多只能上传${this.limit}张图片`)
            },
            initFileList() {
                let value = [];
                if (typeof this.value === 'string') {
                    this.valueType = 'string';
                    if (this.value) {
                        value = this.value.split(',')
                    }
                } else {
                    this.valueType = 'array';
                    value = this.value || [];
                }
                this.fileList = [];
                value.forEach(item => {
                    this.fileList.push({
                        url: item
                    })
                })
            }
        },
        created() {
            this.initFileList()
        },
        watch: {
            value (val) {
                this.initFileList()
            }
        }
    }
</script>

<style>
    .upload-fulled .el-upload--picture-card {
        display: none;
    }
</style>
