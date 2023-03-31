<template>
    <nav class="pagination-wrapper" aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item">
                <button type="button" class="page-link" v-if="page > 1" @click="$emit('goPrev')"> &lt;&lt; </button>
            </li>
            <li class="page-item">
                <span type="button" class=""> {{page}} sur {{pages.length}} </span>
            </li>
            <li class="page-item">
                <button type="button" @click="$emit('goNext')" v-if="page < pages.length" class="page-link"> >> </button>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    name: "Pagination",
    props: {
        page: Number,
        pages: Array
    }
}
</script>

<style scoped>

</style>