<template>
<div>
        <router-link v-bind:to="'/train?c=' + category.id + '&u=0'">{{ category.name }}</router-link>
        <router-link v-bind:to="'/train?c=' + category.id + '&u=1'">Oklarade</router-link>
        <div class="indent">
                <TreeMenu v-for="(category, index) in category.children" v-bind:key="index" v-bind:category="category">
                </TreeMenu>
        </div>
</div>
</template>
<script>
export default {
        name: 'TreeMenu',
        props: ['category']
}
</script>
<style scoped>
div.indent {
        margin-left: 5%;
}
</style>
