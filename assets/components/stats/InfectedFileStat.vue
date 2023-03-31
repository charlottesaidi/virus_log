<template>
    <stat-card v-if="displayedInfectedFiles.length > 0">
        <table>
            <tr v-for="log in displayedInfectedFiles" class="paragraphe small">
                <td>
                    <span class="text-amber">{{ $moment(log.createdAt).format("L HH:mm") }}</span>&nbsp;-&nbsp;
                </td>
                <td>
                    <span class="text-danger">{{ log.numberInfectedFile }}</span>&nbsp;fichiers&nbsp;infectés
                </td>
                <td>
                    &nbsp;&nbsp;ip:&nbsp;<span class="text-success">{{ log.ip }}</span>
                </td>
            </tr>
        </table>
        <Pagination
            :pages="pages"
            :page="page"
            @go-next="() => page++"
            @go-prev="() => page--"
        />
    </stat-card>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import StatCard from "./StatCard.vue";
import Pagination from "../Pagination.vue";

export default defineComponent({
    name: "InfectedFileStat",
    components: {
        Pagination,
        StatCard,
    },
    props: {
        infectedFiles: { type: Array, default: () => null },
    },
    data() {
        return {
            page: 1,
            itemPerPage: 20,
            pages: [] as Number[]
        }
    },
    methods: {
        paginate(): Array<any> {
            let page = this.page;
            let perPage = this.itemPerPage;
            let from = (page * perPage) - perPage;
            let to = (page * perPage);
            return this.infectedFiles.slice(from, to);
        },
        setPages(): void {
            let numberOfPages = Math.ceil(this.infectedFiles.length / this.itemPerPage);
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        }
    },
    computed: {
        displayedInfectedFiles() {
            return this.paginate();
        }
    },
    created() {
        this.setPages();
    }
})
</script>

<style scoped>

</style>