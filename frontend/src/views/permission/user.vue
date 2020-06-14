<template>
    <div class="app-container">
        <el-form inline>
            <el-button size="small" class="fr" type="success" @click="goToAdd">添加用户</el-button>
        </el-form>
        <el-table :data="list" stripe v-loading="tableLoading">
            <el-table-column type="index"></el-table-column>
            <el-table-column prop="name" label="用户名"></el-table-column>
            <el-table-column prop="email" label="邮箱"></el-table-column>
            <el-table-column label="角色名称">
                <template slot-scope="scope">
                    {{scope.row.role.name}}
                </template>
            </el-table-column>
            <el-table-column label="操作">
                <template slot-scope="scope">
                    <el-button type="primary" size="small" @click="goToEdit(scope.row)">编 辑</el-button>
                    <el-button type="danger" size="small" @click="goToDelete(scope.row)">删 除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <pagination :total="total"
                    :page.sync="searchParam.page"
                    :limit.sync="searchParam.page_size"
                    @pagination="getList"></pagination>

        <el-dialog :visible.sync="showDialog" :title="dialogTitle" width="30%">
            <el-form :model="formData" :rules="formRules" ref="userForm" label-width="100px" size="small">
                <el-form-item prop="name" label="名称">
                    <el-input v-model="formData.name" clearable></el-input>
                </el-form-item>
                <el-form-item prop="email" label="邮箱">
                    <el-input v-model="formData.email" clearable></el-input>
                </el-form-item>
                <el-form-item prop="role_id" label="角色">
                    <el-select v-model="formData.role_id" placeholder="请选择" clearable>
                        <el-option
                                v-for="item in allRole"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item v-if="type === 'add'" prop="password" label="密码" required>
                    <el-input v-model="formData.password" type="password" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item v-if="type === 'add'" prop="password_confirmation" label="确认密码" required>
                    <el-input v-model="formData.password_confirmation" type="password" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" size="small" @click="submitUser">确 定</el-button>
                    <el-button size="small" @click="cancelForm">取 消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    import {deepClone} from '../../utils';

    export default {
        name: 'PermissionUser',
        data() {
            const validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入密码'));
                } else {
                    if (this.formData.password_confirmation !== '') {
                        this.$refs.userForm.validateField('password_confirmation');
                    }
                    callback();
                }
            };
            const validatePassConfirm = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请再次输入密码'));
                } else if (value !== this.formData.password) {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };

            return {
                tableLoading: false,
                list: [],
                searchParam: {
                    page: 1,
                    page_size: 10
                },
                total: 0,
                allRole: [],
                showDialog: false,
                dialogTitle: '',

                formData: {
                    id: 0,
                    name: '',
                    email: '',
                    role_id: '',
                    password: '',
                    password_confirmation: ''
                },
                type: '',
                formRules: {
                    name: [
                        {required: true, message: '用户名不能为空', trigger: 'blur'},
                        {min: 1, max: 30, message: '用户名长度不超过30个字符', trigger: 'blur'}
                    ],
                    email: [
                        {type: 'email', message: '请填写正确邮箱', trigger: 'blur'}
                    ],
                    role_id: [
                        {required: true, message: '角色不能为空', trigger: 'blur'}
                    ],
                    password: [
                        {validator: validatePass, trigger: 'blur'},
                        {min: 6, max: 30, message: '密码长度为6-30个字符', trigger: 'blur'}
                    ],
                    password_confirmation: [
                        {validator: validatePassConfirm, trigger: 'blur'}
                    ]
                }
            }
        },
        methods: {
            getList() {
                this.tableLoading = true;
                this.get('/user/list', this.searchParam).then(res => {
                    this.list = res.list;
                    this.total = res.total;
                    this.tableLoading = false;
                })
            },
            goToAdd() {
                this.type = 'add';
                this.dialogTitle = '添加角色';
                this.formData.id = 0;
                this.showDialog = true;
            },
            goToEdit(row) {
                this.type = 'edit';
                this.dialogTitle = '编辑角色';
                const clone = deepClone(row);
                this.formData  = {
                    id: clone.id,
                    name: clone.name,
                    email: clone.email,
                    role_id: clone.role_id
                };
                this.showDialog = true;
            },
            getRoleList() {
                this.get('/role/list').then(res => {
                    this.allRole = res;
                })
            },
            submitUser() {
                this.$refs.userForm.validate(valid => {
                    if (valid) {
                        if (this.type === 'add') {
                            this.post('/user/add', this.formData).then(() => {
                                this.$message.success('用户添加成功');
                                this.getList();
                                this.cancelForm();
                            })
                        } else {
                            this.post('/user/edit', this.formData).then(() => {
                                this.$message.success('用户编辑成功');
                                this.getList();
                                this.cancelForm();
                            })
                        }
                    }
                })
            },
            cancelForm() {
                this.showDialog = false;
                this.$refs.userForm.resetFields();
            },
            goToDelete(row) {
                this.$confirm('此操作将永久删除用户, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.post('/user/delete', {id: row.id}).then(() => {
                        this.$message.success('删除成功');
                        this.getList();
                    })
                })
            }
        },
        created() {
            this.getList();
            this.getRoleList();
        }
    }
</script>

<style scoped>

</style>
