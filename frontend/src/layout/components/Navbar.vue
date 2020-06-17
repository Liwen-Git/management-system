<template>
    <div class="navbar">
        <!-- 菜单展开 缩小 -->
        <hamburger id="hamburger-container" :is-active="sidebar.opened" class="hamburger-container"
                   @toggleClick="toggleSideBar"/>

        <!-- 面包屑 -->
        <breadcrumb id="breadcrumb-container" class="breadcrumb-container"/>

        <div class="right-menu">
            <template v-if="device!=='mobile'">
                <search id="header-search" class="right-menu-item"/>

                <error-log class="errLog-container right-menu-item hover-effect"/>

                <screenfull id="screenfull" class="right-menu-item hover-effect"/>

                <el-tooltip content="Global Size" effect="dark" placement="bottom">
                    <size-select id="size-select" class="right-menu-item hover-effect"/>
                </el-tooltip>

            </template>

            <el-dropdown class="avatar-container right-menu-item hover-effect" trigger="click">
                <div class="avatar-wrapper">
                    <img :src="avatar+'?imageView2/1/w/80/h/80'" class="user-avatar">
                    <i class="el-icon-caret-bottom"/>
                </div>
                <el-dropdown-menu slot="dropdown">
                    <router-link to="/profile/index">
                        <el-dropdown-item>个人简介</el-dropdown-item>
                    </router-link>
                    <router-link to="/">
                        <el-dropdown-item>仪表盘</el-dropdown-item>
                    </router-link>
<!--                    <a target="_blank" href="https://github.com/PanJiaChen/vue-element-admin/">-->
<!--                        <el-dropdown-item>Github</el-dropdown-item>-->
<!--                    </a>-->
<!--                    <a target="_blank" href="https://panjiachen.github.io/vue-element-admin-site/#/">-->
<!--                        <el-dropdown-item>Docs</el-dropdown-item>-->
<!--                    </a>-->
                    <el-dropdown-item @click.native="showDialog = true">
                        <span style="display:block;">修改密码</span>
                    </el-dropdown-item>
                    <el-dropdown-item divided @click.native="logout">
                        <span style="display:block;">退 出</span>
                    </el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>

        <!--修改密码弹窗-->
        <el-dialog :visible.sync="showDialog" title="修改密码" width="30%">
            <el-form :model="formData" :rules="formRules" ref="passwordForm" label-width="100px" size="small">
                <el-form-item prop="password" label="密码" required>
                    <el-input v-model="formData.password" type="password" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item prop="password_confirmation" label="确认密码" required>
                    <el-input v-model="formData.password_confirmation" type="password" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" size="small" @click="changePassword">确 定</el-button>
                    <el-button size="small" @click="cancelForm">取 消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import Breadcrumb from '@/components/Breadcrumb'
    import Hamburger from '@/components/Hamburger'
    import ErrorLog from '@/components/ErrorLog'
    import Screenfull from '@/components/Screenfull'
    import SizeSelect from '@/components/SizeSelect'
    import Search from '@/components/HeaderSearch'

    export default {
        data() {
            const validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入密码'));
                } else {
                    if (this.formData.password_confirmation !== '') {
                        this.$refs.passwordForm.validateField('password_confirmation');
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
                showDialog: false,
                formData: {
                    password: '',
                    password_confirmation: ''
                },
                formRules: {
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
        components: {
            Breadcrumb,
            Hamburger,
            ErrorLog,
            Screenfull,
            SizeSelect,
            Search
        },
        computed: {
            ...mapGetters([
                'sidebar',
                'avatar',
                'device'
            ])
        },
        methods: {
            toggleSideBar() {
                this.$store.dispatch('app/toggleSideBar')
            },
            async logout() {
                await this.$store.dispatch('user/logout')
                this.$router.push(`/login?redirect=${this.$route.fullPath}`)
            },
            changePassword() {
                this.$refs.passwordForm.validate(valid => {
                    if (valid) {
                        this.post('/user/change/password', this.formData).then(() => {
                            this.$message.success('修改密码成功');
                            this.cancelForm();
                        })
                    }
                })
            },
            cancelForm() {
                this.showDialog = false;
                this.$refs.passwordForm.resetFields();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .navbar {
        height: 50px;
        overflow: hidden;
        position: relative;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0, 21, 41, .08);

        .hamburger-container {
            line-height: 46px;
            height: 100%;
            float: left;
            cursor: pointer;
            transition: background .3s;
            -webkit-tap-highlight-color: transparent;

            &:hover {
                background: rgba(0, 0, 0, .025)
            }
        }

        .breadcrumb-container {
            float: left;
        }

        .errLog-container {
            display: inline-block;
            vertical-align: top;
        }

        .right-menu {
            float: right;
            height: 100%;
            line-height: 50px;

            &:focus {
                outline: none;
            }

            .right-menu-item {
                display: inline-block;
                padding: 0 8px;
                height: 100%;
                font-size: 18px;
                color: #5a5e66;
                vertical-align: text-bottom;

                &.hover-effect {
                    cursor: pointer;
                    transition: background .3s;

                    &:hover {
                        background: rgba(0, 0, 0, .025)
                    }
                }
            }

            .avatar-container {
                margin-right: 30px;

                .avatar-wrapper {
                    margin-top: 5px;
                    position: relative;

                    .user-avatar {
                        cursor: pointer;
                        width: 40px;
                        height: 40px;
                        border-radius: 10px;
                    }

                    .el-icon-caret-bottom {
                        cursor: pointer;
                        position: absolute;
                        right: -20px;
                        top: 25px;
                        font-size: 12px;
                    }
                }
            }
        }
    }
</style>
