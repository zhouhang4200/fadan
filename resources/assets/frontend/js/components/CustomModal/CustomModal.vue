<template>
    <Modal v-model="visible"  closable="closable" class="custom-modal">
        <p slot="header" style="color:#f60;text-align:center">
            <Icon type="ios-information-circle"></Icon>
            <span>Delete confirmation</span>
        </p>
        <div style="text-align:center">
            <p>After this task is deleted, the downstream 10 tasks will not be implemented.</p>
            <p>Will you delete it?</p>
        </div>

    </Modal>
</template>

<style scoped>
     .ivu-modal-wrap > .ivu-modal-footer {
        border-top:0px;
    }
</style>

<script>
    export default {
        name: 'customModal',
        data () {
            return {
                closable:false,
                visible: true,
                content: '',
            }
        },
        methods: {

        },
        mounted() {

        },
        created() {
            // this.$Modal.confirm({
            //     title: '操作提示',
            //     content: '您确定要删除吗？',
            //     onOk: () => {
            //     },
            //     onCancel: () => {
            //     }
            // });
        },
    }
</script>
