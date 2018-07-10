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
        },
        enableFilter: false,
        filterString: ''
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
        },
        recipesToShow () {
            let recipes = {};
            for (let i in this.recipes) {
                let recipe = this.recipes[i];
                if (recipes[recipe.officialPackageName] === undefined) {
                    recipes[recipe.officialPackageName] = recipe;
                    recipes[recipe.officialPackageName].versions = [];
                }

                recipes[recipe.officialPackageName].versions.push(recipe.version);
            }

            recipes = Object.keys(recipes).map(function (i) { return recipes[i]; });

            recipes.sort((a, b) => {
                return (a.officialPackageName < b.officialPackageName ? -1 :
                    (a.officialPackageName > b.officialPackageName ? 1 : 0));
            });

            if (this.enableFilter && this.filterString.length > 0) {
                let filterString = this.filterString.toLowerCase();
                recipes = recipes.filter((recipe) => {
                    let searchable = recipe.author + recipe.package;
                    if (recipe.manifestValid === true && recipe.manifest.aliases !== undefined) {
                        searchable = searchable + recipe.manifest.aliases.join();
                    }
                    return searchable.toLowerCase().search(filterString) !== -1;
                });
            }

            return recipes;
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
            return this.buildRecipeRepoUrl(recipe) + '/' + recipe.version
        },
        recipeVersionUrl (recipe, version) {
            return this.buildRecipeRepoUrl(recipe) + '/' + version
        },
        buildRecipeRepoUrl (recipe) {
            let repoUrl, pattern, gitServerHostName

            repoUrl = recipe.repo.url.replace('.git', '')

            pattern = /^git\@[a-z.]*\:/
            if (pattern.test(repoUrl)) {
                gitServerHostName = pattern.exec(repoUrl)[0].replace('git@', '').replace(':', '')
                repoUrl.replace(pattern, 'https://' + gitServerHostName + '/')
            }

            pattern = /^https?\:\/\/[a-z.]*/
            gitServerHostName = pattern.exec(repoUrl)[0].replace(/https?\:\/\//, '')

            switch (gitServerHostName) {
                case 'bitbucket.org':
                    return repoUrl + '/src/master/' + recipe.officialPackageName
                default:
                    return repoUrl + '/tree/master/' + recipe.officialPackageName
            }
        },
        showSearch () {
            this.enableFilter = true;
            this.$nextTick(() => {
                this.$refs.searchField.focus();
            });
        },
        hideSearch () {
            this.enableFilter = false;
            this.filterString = '';
        },
        versionSort (versionArray) {
            versionArray.sort((a,b) => {
                return (a < b ? 1 : (
                    a > b ? -1 : 0
                ));
            });
            return versionArray;
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
