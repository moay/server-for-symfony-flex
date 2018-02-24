# symfony-flex-server
A self hosted server for Symfony Flex allowing private and/or customized recipes, proxy and caching functionality for the official endpoints.

![ui](https://user-images.githubusercontent.com/3605512/36627099-eb239f48-193d-11e8-919a-d98003696d7b.png)


## Work in progress
Please come back later.

___

### Features

* private recipes: The server enables you to use your own recipes for your own packages, even with Satis or Private Packagist.
* seamless integration: The server integrates with the official symfony.sh-endpoint and will serve the official recipes as well as your own ones.
* caching and mirroring: The server can easily be configured to mirror the official repos so that you are completely independent.

### Setup

// TODO

### Update the recipes

The recipe repos should be updated every now and then. In order to have the flex server download your recipes when they change, you can use a webhook.

In Github or Gitlab you can use the url `https://your.domain/webhook/update` to have the flex server update the repos. 

### Using the server

Using the server in your Symfony project is as easy as adding the proper endpoint to your `composer.json`:

    {
        ...
        "symfony": {
            "endpoint": "https://your.domain.com"
        }
    }

### Running the server locally

Running the server locally is possible by using symfony's server command: `php bin/console server:start` (oder `server:run` for a temporary instance).
Make sure to allow connections over http to composer (if using localhost) by adding this to the project's `composer.json`:

    {
        ...
        "config": {
            "secure-http": "false"
        }
    }
    
*It is not recommended to run the server locally. The best setup would be a private server behind a firewall.*

### Configuration

The server comes well configured but the configuration can be tweaked. You should set your private recipes repo, all other options are otional.

In order to change the configuration, alter the file `/config/services.yaml`.

    recipe_repo_private: The url to your private recipe repo
    recipe_repo_official: The url to the official recipe repo
    recipe_repo_contrib: The url to the contrib recipe repo

You probably will only need to setup the private repo.

    official_endpoint: The official endpoint, should probably not be touched.
    proxy_official_endpoint: Boolean. If true, the official endpoint will be seamlessly integrated.
    cache_official_endpoint: Boolean. If true, the official endpoint will be cached.

If the official endpoint is enabled and cached, the server will cache the requests to the endpoint. If there is no network at any point, the last cached response will be returned. 

    mirror_official_recipes: Boolean. If true, the official recipes repo will be mirrored locally.
    mirror_contrib_recipes: Boolean. If true, the contrib recipes repo will be mirrored locally.

If mirroring is enabled, you are completely detached from github. Make sure to update the repos every now and then using the commands below.

### Available commands

In order to simplify configuration and handling, there is a range of commands that will allow you to manage the server.

#### Status

    php bin/console system:status

This command will print out an overview of the current configuration and the recipe repos.

#### Recipe repo handling

##### Initializing

    php bin/console recipes:initialize

This command will initialize the repos according to the configuration parameters and download them if needed.

##### Updating

    php bin/console recipes:update

Will run a `git pull` on the repos. Repos that haven't been initialized will be initialized before the pull.

During the update, a backup of the current local repo will be made. It will be restored if the update fails.

##### Resetting

    php bin/console recipes:reset

This will basically delete the current repo folder an reinitialize the repo. There is **no** automatic rollback in case the cloning fails.

##### Deleting

    php bin/console recipes:delete

This will delete the local repo folder.

##### Select affected repos

All of the above repo handling commands can be limited to certain repos by providing a selector. Like so:
    
    # Private repo only
    php bin/console recipes:initialize private
    
    # Private and contrib repo
    php bin/console recipes:initialize private contrib