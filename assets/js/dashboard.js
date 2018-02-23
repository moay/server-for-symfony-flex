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

    computed: {
        activeRepos () {
            if (Object.keys(this.status).length === 0) {
                return [];
            }
            let repos = [this.status.repos.private];
            if (this.status.config.mirrorOfficial) {
                repos.push(this.status.repos.official);
            }
            if (this.status.config.mirrorContrib) {
                repos.push(this.status.repos.contrib);
            }
            return repos;
        }
    },

    methods: {
        loadUiData () {
            this.$http.get('ui/data').then((response) => {
                this.status = response.body.status;
                this.recipes = response.body.recipes;
            })
        },
        recipeUrl (recipe) {
            return recipe.repo.url + '/tree/master/' + recipe.officialPackageName + '/' + recipe.version
        }
    },

    filters: {
        capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
        },
        fromNow (dateString) {
            let date = new Date(dateString);
            let moment = require('moment');
            return moment(date).fromNow();
        }
    },

    mounted () {
        this.loadUiData();
    }
})