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
                        <tr v-for="(pair, index) in pairs" v-bind:key="index"><td>{{ index+1 }}</td><td><input v-model="pair.a" v-bind:ref="'a' + index" class="halfpair" v-tooltip="pair.aSimilar" v-bind:class="{'exists' : pair.aExists}" name="a[]" type="text"></td><td><input class="halfpair" v-bind:class="{'exists' : pair.bExists}" v-tooltip="pair.bSimilar" v-model="pair.b" v-bind:ref="'b' + index" v-on:keydown.tab="addRow(pair)" name="b[]" type="text"></td></tr>
                </table>
                <div class="g-recaptcha" data-sitekey=""></div>
                Password<input name="password"><br>
                <button v-on:click.prevent="addRow(rows)">Add row</button>
                <button v-on:click.prevent="save()">Save</button><br>
        </form>
</div>
</template>
<script>
import VTooltip from 'v-tooltip'

export default {
        name: 'add',
        data: function() {
                return {
                        categoryLevels: 1,
                        pairs: [{a: '', b: '', aExists: false, bExists: false, aSimilar: '', bSimilar: ''}],
                        categories: []
                }
        },
        components: {
                VTooltip
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
                addRow(pair) {
                        if (pair === this.pairs[this.pairs.length - 1]) {
                                this.pairs.push({a: '', b: '', aExists: false, bExists: false, aSimilar: '', bSimilar: ''})

                                // Set focus on the added element
                                var refs = this.$refs
                                var lastIndex = (this.pairs.length - 1)
                                setTimeout(function() {
                                        refs['a' + lastIndex][0].focus()
                                }, 100)
                        }
                        this.check(pair)
                },
                save() {
                        var formData = new FormData(this.$refs.glosForm)
                        formData.append('stuff', 'glosor')
                        this.$http.post('/cgi/save.php', formData, {emulateJSON: true}).then(response => {
                                if (response.body === 0) {
                                        this.pairs = [{a: '', b: '', aExists: false, bExists: false, aSimilar: '', bSimilar: ''}]
                                }
                                console.log(response)
                        }, response => {
                                console.log(response)
                        })
                },
                check(pair) {
                        this.$http.get('/cgi/get.php', {params: {stuff: 'similar', a: pair.a, b: pair.b}}).then(response => {
                                console.log(response)
                                pair.bExists = response.body.bExists
                                pair.aExists = response.body.aExists
                                pair.aSimilar = response.body.aSimilar
                                pair.bSimilar = response.body.bSimilar
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
input.exists {
        background-color: yellow;
}
table {
        width: 100%;
}

.tooltip {
  display: block !important;
  z-index: 10000;
}

.tooltip .tooltip-inner {
  background: black;
  color: white;
  border-radius: 16px;
  padding: 5px 10px 4px;
}

.tooltip .tooltip-arrow {
  width: 0;
  height: 0;
  border-style: solid;
  position: absolute;
  margin: 5px;
  border-color: black;
  z-index: 1;
}

.tooltip[x-placement^="top"] {
  margin-bottom: 5px;
}

.tooltip[x-placement^="top"] .tooltip-arrow {
  border-width: 5px 5px 0 5px;
  border-left-color: transparent !important;
  border-right-color: transparent !important;
  border-bottom-color: transparent !important;
  bottom: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}

.tooltip[x-placement^="bottom"] {
  margin-top: 5px;
}

.tooltip[x-placement^="bottom"] .tooltip-arrow {
  border-width: 0 5px 5px 5px;
  border-left-color: transparent !important;
  border-right-color: transparent !important;
  border-top-color: transparent !important;
  top: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}

.tooltip[x-placement^="right"] {
  margin-left: 5px;
}

.tooltip[x-placement^="right"] .tooltip-arrow {
  border-width: 5px 5px 5px 0;
  border-left-color: transparent !important;
  border-top-color: transparent !important;
  border-bottom-color: transparent !important;
  left: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}

.tooltip[x-placement^="left"] {
  margin-right: 5px;
}

.tooltip[x-placement^="left"] .tooltip-arrow {
  border-width: 5px 0 5px 5px;
  border-top-color: transparent !important;
  border-right-color: transparent !important;
  border-bottom-color: transparent !important;
  right: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}

.tooltip.popover .popover-inner {
  background: #f9f9f9;
  color: black;
  padding: 24px;
  border-radius: 5px;
  box-shadow: 0 5px 30px rgba(black, .1);
}

.tooltip.popover .popover-arrow {
  border-color: #f9f9f9;
}

.tooltip[aria-hidden='true'] {
  visibility: hidden;
  opacity: 0;
  transition: opacity .15s, visibility .15s;
}

.tooltip[aria-hidden='false'] {
  visibility: visible;
  opacity: 1;
  transition: opacity .15s;
}
</style>
