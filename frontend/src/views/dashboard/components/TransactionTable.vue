<template>
    <el-table :data="list" style="width: 100%;padding-top: 15px;">
        <el-table-column label="Order_No" min-width="200">
            <template slot-scope="scope">
                {{ scope.row.order_no | orderNoFilter }}
            </template>
        </el-table-column>
        <el-table-column label="Price" width="195" align="center">
            <template slot-scope="scope">
                ¥{{ scope.row.price | toThousandFilter }}
            </template>
        </el-table-column>
        <el-table-column label="Status" width="100" align="center">
            <template slot-scope="{row}">
                <el-tag :type="row.status | statusFilter">
                    {{ row.status }}
                </el-tag>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
    import {transactionList} from '@/api/remote-search'

    export default {
        filters: {
            statusFilter(status) {
                const statusMap = {
                    success: 'success',
                    pending: 'danger'
                }
                return statusMap[status]
            },
            orderNoFilter(str) {
                return str.substring(0, 30)
            }
        },
        data() {
            return {
                list: null
            }
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.list = [
                    {
                        order_no: 'No1231',
                        timestamp: this.$moment().format('lll'),
                        username: 'lee',
                        price: '1000.00',
                        status: 'success'
                    },
                    {
                        order_no: 'No1232',
                        timestamp: this.$moment().format('lll'),
                        username: 'iee',
                        price: '2000.00',
                        status: 'pending'
                    }
                ]
            }
        }
    }
</script>
