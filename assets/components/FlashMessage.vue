<template>
    <div :class="'flash ' + className + ' ' + classType">
        {{ message}}
        <slot/>
    </div>
</template>

<script>
export default {
    name: "FlashMessage",
    props: {
        type: String,
        message: String,
        className: String | null
    },
    data() {
        return {
            classType: ''
        }
    },
    created() {
        switch(this.type) {
            case 'error':
                this.classType = 'flash--danger'
                break;
            case 'success':
                this.classType = 'flash--success'
                break;
            case 'warning':
                this.classType = 'flash--warning'
                break;
            default:
                this.classType = 'flash--info'
        }
    }
}
</script>

<style scoped>

</style>