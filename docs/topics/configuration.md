# Configuration

The server comes well configured but the configuration can be tweaked. You should set your private recipes repo, all other options are optional.

In order to change the configuration, alter the file `/config/parameters.yaml`. Some of the values can be provided in environment variables or via .env-file, take a look at the contained `.env.dist` for helpful hints.

### Private recipes repo

Your private recipes repo must be a git repo. It must be available via git, so it should be either a public repo or you should ensure that your server has access to it, probably by setting up some sort of SSH keys. In order to make the server use your repo, set this parameter:

    env(FLEX_RECIPE_REPO_PRIVATE): The url to your private recipe repo
    
The url provided should be a *https* url, but most valid git-urls will work. Make sure to provide the right url structure, here are some examples:

*  `https://github.com/moay/demo-recipes`
*  `https://github.com/moay/demo-recipes.git`
*  `https://bitbucket.org/mvmoay/demo-recipes`
*  `git@github.com:moay/demo-recipes.git`
*  `git@private-gitlab.com:1234/moay/demo-recipes.git`

Github has proven to be quite good in resolving git requests, other providers or git servers may be pickier. If you run into problems, try to play with '.git', the protocol prefix or the type of url provided.

After changing the configuration, you'll need to update the repos. Whenever you change the repo entirely, it is recommended to completely reset the private repo by executing the command `php bin/console recipes:reset private`.

Read more about these commands [here](commands.md).

### Official endpoint configuration options

* If you want your server instance to **not** act as a proxy for the official endpoint, set `proxy_official_endpoint` to false. The default behaviour is to do so, because otherwise, Symfony probably won't work properly. You should have good reasons in order to disable it.     
* If you don´t want the server to cache requests it makes to the official endpoint, set `cache_official_endpoint` to false. You won't notice any changes until network fails to load the official endpoint.
* If (for some reason) you want to have any other official endpoint than symfony.sh, change `env(FLEX_OFFICIAL_ENDPOINT)`. You could setup a working endpoint chain by providing the url to a second instance of this server.

### Mirroring the official recipe repos

Having a local copy of the official repos might be something you consider. You can enable local mirroring of both repos using these options:

    mirror_official_recipes: Boolean. If true, the official recipes repo will be mirrored locally.
    mirror_contrib_recipes: Boolean. If true, the contrib recipes repo will be mirrored locally.
    
Also, you might want to use your own forks of the official repos, you could use them by changing two options:

    env(FLEX_RECIPE_REPO_OFFICIAL): The url to the official recipe repo
    env(FLEX_RECIPE_REPO_CONTRIB): The url to the contrib recipe repo

If you use both the proxy functionality **and** custom official repos, the official endpoint responses will be used only for recipes that are not available in your repos.

**Attention:** If a newer version of a recipe is published on the official endpoint, it will be considered the correct recipe for the request even if you have an older recipe in your local repos. 
