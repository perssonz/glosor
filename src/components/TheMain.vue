<template>
<div class="main">
        <router-link to="/add">Add stuff</router-link>
        <h1>glosor</h1>
        <h2>Select subject/category</h2>
        <TreeMenu v-for="(category, index) in categories" v-bind:key="index" v-bind:category="category">
        </TreeMenu>
</div>
</template>
<script>

import TreeMenu from '@/components/TreeMenu'

export default {
        name: 'TheMain',
        components: {
                TreeMenu
        },
        data: function() {
                return {
                        categories: []
                }
        },
        created: function() {
                this.$http.get('/cgi/get.php', {params: {stuff: 'categories_tree'}}).then(response => {
                        console.log(response)
                        this.categories = response.body
                }, response => {
                        console.log(response)
                })
        }
}
</script>
<style scoped>
div.main {
        width: 50%;
        margin: 0 auto;
}
</style>
