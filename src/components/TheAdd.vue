<template>
<div class="main">
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
                        <tr v-for="n in rows" v-bind:key="n"><td>{{ n }}</td><td><input v-bind:ref="'a' + n" class="halfpair" name="a[]" type="text"></td><td><input class="halfpair" v-bind:ref="'b' + n" v-on:keydown.tab="addRow(n)" name="b[]" type="text"></td></tr>
                </table>
                <div class="g-recaptcha" data-sitekey="6LcvM3cUAAAAAHVvDTd9rMeda0MeLivlFHVkWDDy"></div>
                Password<input name="password"><br>
                <button v-on:click.prevent="addRow(rows)">Add row</button>
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

                                // Set focus on the added element
                                var refs = this.$refs
                                var rows = this.rows
                                setTimeout(function() {
                                        refs['a' + rows][0].focus()
                                }, 100)
                                this.check(refs['b' + (rows - 1)][0].value)
                        }
                },
                save() {
                        var formData = new FormData(this.$refs.glosForm)
                        this.$http.post('/cgi/save.php', formData, {emulateJSON: true}).then(response => {
                                if (response.body === 0) {
                                        this.rows = 1
                                } else {
                                        alert(response.body)
                                }
                                console.log(response)
                        }, response => {
                                console.log(response)
                        })
                },
                check(b) {
                        this.$http.get('/cgi/get.php', {params: {stuff: 'similar', b: b}}).then(response => {
                                console.log(response)
                        }, response => {
                                console.log(response)
                        })
                }
        }
}
</script>
<style scoped>
div.main {
        width: 70%;
        margin: 0 auto;
}
input.halfpair {
        width: 100%;
}
table {
        width: 100%;
}
</style>
