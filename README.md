# symfony-flex-server
A self hosted server for Symfony Flex allowing private and/or customized recipes, proxy and caching functionality for the official endpoints.

## Work in progress
Please come back later.

### Available commands

In order to simplify configuration and handling of the server, there are several commands that will allow you to manage the server.

#### Status

    php bin/console system:status

The `system:status` command will print out an overview of the current configuration and the recipe repos.

#### Recipe repo handling

##### Initializing

    php bin/console recipes:initialize

This command will initialize the repos according to the configuration parameters and download them if needed.

##### Updating

In order to update initialized repos, run

    php bin/console recipes:update

which will run a `git pull` on the repos. Repos that haven't been initialized will be initialized before the pull.

During the update, a backup of the current local repo will be made. It will be restored if the update fails.

##### Resetting

If you ever need to completely reset a repo, use

    php bin/console recipes:reset

which will basically delete the current repo folder an reinitialize the repo.

##### Deleting

    php bin/console recipes:delete

This will delete the local repo folder.

##### Selecting repos

All of the above repo handling commands can be limited to certain repos by providing a selector. Like so:

    php bin/console recipes:initialize private

for the private repo only. Or

    php bin/console recipes:initialize private contrib

for the private and the contrib repo.