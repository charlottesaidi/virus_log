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
        message: String
    },
    data() {
        return {
            className: String,
            classType: String
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
            default:
                this.classType = 'flash--info'
        }
    }
}
</script>

<style scoped>

</style>