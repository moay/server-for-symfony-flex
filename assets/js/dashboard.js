import Vue from 'vue'
import Vuetify from 'vuetify'
import VueResource from 'vue-resource'
import 'vuetify/dist/vuetify.min.css'
import './../css/dashboard.css'

Vue.use(Vuetify)
Vue.use(VueResource)

let vm = new Vue({
    el: '#dashboard',

    delimiters: ['${', '}'],

    data: {
        status: {},
        recipes: {},
        repoColors: {
            private: 'cyan',
            official: 'green',
            contrib: 'orange'
        }
    },

    mounted () {
        this.$http.get('ui/data').then((response) => {
            this.status = response.body.status;
            this.recipes = response.body.recipes;
        })

    }
})