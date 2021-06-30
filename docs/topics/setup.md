# Setup

Setting up the server should be quick and easy. These are the necessary steps:

1. [Download](https://github.com/moay/server-for-symfony-flex/releases) the project from Github (or `git clone https://github.com/moay/server-for-symfony-flex`)
2. Navigate to the project folder and run `composer install`.
3. Copy the `.env` file to `.env.local` and setup your project.
4. Setup the `APP_ENV` properly. This can be done in the `.env.local` file or on the hosting. Setting it to `prod` is recommended.
5. Run `php bin/console recipes:initialize` in order to download your recipes.
6. In order to have a working UI, you'll need to build the assets that come with the project. Make sure to run node in any version higher than 10 (tested up to 15) an run `npm install` followed by `npm run build` to build the needed files.

*Of course, you should deploy the project to where it will be hosted (probably before step 2).*

That's it, you should be up and running. You might want to [tweak the configuration](configuration.md) or to setup automatic recipe updates.

Installing a monitoring tool (like [Sentry](https://sentry.io)) is recommended.

### Updating from v1.3 to v1.4

We switched from composer v1 to v2. Make sure to run composer v2 when running `composer install`. Also if you run into this error:
```
Failed to create closure from callable: class 'Http\HttplugBundle\Discovery \ConfiguredClientsStrategy' does not have a method 'onEvent'
```
you'll have to delete your caches. Should be possible with:

```
rm -rf var/cache/* && bin/console cache:clear
```

Running another `composer install` after this is recommended.

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

#### Local http

Make sure to allow connections over http to composer (if using localhost) by adding this to the project's `composer.json`:

    {
        ...
        "config": {
            "secure-http": false
        }
    }
    
*It is not recommended to run the server locally. The best setup would be a private server behind a firewall.*

#### With Symfony CLI

You can also run the server using the [Symfony CLI](https://symfony.com/download) by using the command `symfony serve` or `symfony serve -d` to run it in the background. This should enable https by default. This was not tested thoroughly, but a proper hosting should be preferred anyway.

### Running the server using docker

A docker setup is provided. Take a look at the given `docker-compose.yml`. The setup of dependencies (both node and PHP) as well as asset building is included in the setup.