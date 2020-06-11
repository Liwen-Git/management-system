<template>
    <div class="app-container">
        <el-table :data="treeList" row-key="id" border :tree-props="{children: 'children', hasChildren: 'hasChildren'}" :v-loading="tableLoading" default-expand-all>
            <el-table-column prop="name" label="权限名称"></el-table-column>
            <el-table-column prop="url" label="权限"></el-table-column>
            <el-table-column prop="type" label="类型">
                <template slot-scope="scope">
                    <span v-if="scope.row.type === 1">菜单</span>
                    <span v-else-if="scope.row.type === 2">方法</span>
                    <span v-else>---</span>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="300">
                <template slot-scope="scope">
                    <el-button size="small" type="success" v-if="scope.row.level <= 2" @click="addClick(scope.row)">添加子权限</el-button>
                    <el-button size="small" type="primary" v-if="scope.row.level > 0" @click="editClick(scope.row)">编辑</el-button>
                    <el-button size="small" type="danger" v-if="scope.row.level > 0" @click="deleteClick(scope.row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <el-dialog :visible.sync="showDialog" :title="dialogTitle" width="30%" @close="cancelForm">
            <el-form ref="menuForm" :model="formData" :rules="formRules" label-width="80px" size="small">
                <el-form-item prop="name" label="权限名称">
                    <el-input v-model="formData.name"></el-input>
                </el-form-item>
                <el-form-item prop="url" label="权限">
                    <el-input v-model="formData.url"></el-input>
                </el-form-item>
                <el-form-item prop="type" label="类型">
                    <el-radio v-model="formData.type" :label="1">菜单</el-radio>
                    <el-radio v-model="formData.type" :label="2">方法</el-radio>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" size="small" @click="submitMenu">确 定</el-button>
                    <el-button size="small" @click="cancelForm">取 消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>

    </div>
</template>

<script>
    export default {
        name: 'PermissionList',
        data() {
            return {
                tableLoading: false,
                treeList: [],

                showDialog: false,
                dialogTitle: '添加权限',

                formData: {
                    id: '',
                    name: '',
                    url: '',
                    type: 1,
                    pid: 0,
                    level: 1
                },
                formRules: {
                    name: [
                        {required: true, message: '权限名称不能为空', trigger: 'blur'}
                    ],
                    url: [
                        {required: true, message: '权限不能为空', trigger: 'blur'}
                    ],
                    type: [
                        {required: true, message: '类型不能为空', trigger: 'blur'}
                    ]
                }
            }
        },
        methods: {
            getList() {
                this.tableLoading = true;
                this.get('/permission/list').then(data => {
                    this.treeList = [{
                        id: '0',
                        name: '全部',
                        url: '---',
                        type: '---',
                        level: 0,
                        children: data
                    }];
                    this.tableLoading = false;
                })
            },
            addClick(row) {
                this.dialogTitle = '添加权限';
                this.formData.id = '';
                this.formData.pid = parseInt(row.id);
                this.formData.level = parseInt(row.level) + 1;
                this.showDialog = true;
            },
            cancelForm() {
                this.showDialog = false;
                this.$refs.menuForm.resetFields();
            },
            submitMenu() {
                this.$refs.menuForm.validate(valid => {
                    if (valid) {
                        if (this.formData.id) {
                            this.post('/permission/edit', this.formData).then(() => {
                                this.$message.success('编辑成功');
                                this.getList();
                                this.cancelForm();
                            })
                        } else {
                            this.post('/permission/add', this.formData).then(() => {
                                this.$message.success('添加成功');
                                this.getList();
                                this.cancelForm();
                            })
                        }
                    }
                })
            },
            editClick(row) {
                const item = JSON.parse(JSON.stringify(row));

                if (item.children) {
                    delete item.children;
                }
                delete item.created_at;
                delete item.updated_at;
                this.dialogTitle = '编辑权限';
                this.formData = item;
                this.showDialog = true;
            },
            deleteClick(row) {
                this.$confirm('此操作将永久删除权限, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.post('/permission/delete', {id: row.id}).then(() => {
                        this.$message.success('删除成功');
                        this.getList();
                    })
                })
            }
        },
        created() {
            this.getList();
        }
    }
</script>

<style scoped>

</style>