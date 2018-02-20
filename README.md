# symfony-flex-server
A self hosted server for Symfony Flex allowing private and/or customized recipes, proxy and caching functionality for the official endpoints.

## Work in progress
Please come back later.

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