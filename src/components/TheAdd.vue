<template>
<div>
        <h1>Add</h1>
        <router-link to="/">Back</router-link>
        <form ref="glosForm">
                <h2>Category</h2>
                <input v-for="n in categoryLevels" v-bind:list="'dlist' + n" name="category[]" v-bind:key="n" type="text"><button v-on:click.prevent="addSubcategory()">Add subcategory</button><br>
                <datalist v-for="(level, index) in categories" v-bind:key="index + '-datalist'" v-bind:id="'dlist' + (index + 1)">
                        <option v-for="(category, index) in level" v-bind:key="index + '-option'">{{ category.name }}</option>
                </datalist>
                <h2>Pairs</h2>
                <table>
                        <tr v-for="n in rows" v-bind:key="n"><td><input name="a[]" type="text"></td><td><input v-on:keydown.tab="addRow(n)" name="b[]" type="text"></td></tr>
                </table>
                <button v-on:click.prevent="addRow()">Add row</button>
                <button v-on:click.prevent="save()">Save</button><br>
        </form>
</div>
</template>
<script>
export default {
        name: 'add',
        data: function() {
                return {
                        categoryLevels: 1,
                        rows: 1,
                        categories: []
                }
        },
        created: function() {
                this.$http.get('/cgi/get.php', {params: {stuff: 'categories_levels'}}).then(response => {
                        console.log(response)
                        this.categories = response.body
                }, response => {
                        console.log(response)
                })
        },
        methods: {
                addSubcategory() {
                        this.categoryLevels++
                },
                addRow(n) {
                        if (n === this.rows) {
                                this.rows++
                        }
                },
                save() {
                        var formData = new FormData(this.$refs.glosForm)
                        this.$http.post('/cgi/save.php', formData, {emulateJSON: true}).then(response => {
                                console.log(response)
                                this.rows = 1
                        }, response => {
                                console.log(response)
                        })
                }
        }
}
</script>
<style scoped>
table {
        margin-left: auto;
        margin-right: auto;
}
</style>
