<template>
<div class="main">
        <router-link to="/">Back</router-link><br>
        <button v-if="!started" v-on:click="start()">Börja!</button>
        <h2 v-if="started">{{ index + 1 }}/{{ deck.length }}</h2>
        <h1 v-if="started">{{ a }}</h1>
        <input class="answer" type="text" v-if="started" v-model="answer" v-on:keyup="check()" v-on:keydown.enter="tip()"><br>
        <span v-if="started">{{ tiptext }}</span>

</div>
</template>
<script>
export default {
        name: 'TheTrain',
        props: ['category'],
        data: function() {
                return {
                        pairs: [],
                        a: '',
                        started: false,
                        index: -1,
                        answer: '',
                        tiptext: '',
                        deck: []
                }
        },
        created: function() {
                this.$http.get('/cgi/get.php', {params: {stuff: 'glosor', category: this.category}}).then(response => {
                        console.log(response)
                        this.pairs = response.body
                }, response => {
                        console.log(response)
                })
        },
        methods: {
                start: function() {
                        if (this.pairs.length !== 0) {
                                this.started = true

                                // Create deck
                                for (var i = 0; i < this.pairs.length; i++) {
                                        this.deck.push(i)
                                }

                                // Shuffle deck
                                for (i = this.deck.length - 1; i > 0; i--) {
                                        var j = Math.floor(Math.random() * (i + 1))
                                        var temp = this.deck[i]
                                        this.deck[i] = this.deck[j]
                                        this.deck[j] = temp
                                }
                                this.next()
                        }
                },
                next: function() {
                        this.index++
                        if (this.index < this.deck.length) {
                                // Next flashcard
                                this.a = this.pairs[this.deck[this.index]].a
                                this.answer = ''
                                this.tiptext = ''
                        } else {
                                // Done, route back to startpage
                                this.$router.push('/')
                        }
                },
                check: function() {
                        if (this.answer === this.pairs[this.deck[this.index]].b) {
                                this.next()
                        }
                },
                tip: function() {
                        this.tiptext = this.pairs[this.deck[this.index]].b

                        // If you need a tip, recycle this card to the end
                        this.deck.push(this.deck[this.index])
                }
        }
}
</script>
<style scoped>
div.main {
        width: 60%;
        margin: 0 auto;
}
input.answer {
        font-size: 20px;
        width: 100%;
}
</style>
