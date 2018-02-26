# Setup

Setting up the server should be quick and easy. These are the necessary steps:

1. [Download](https://github.com/moay/symfony-flex-server/releases) the project from Github (or `git clone https://github.com/moay/symfony-flex-server`)
2. Navigate to the project folder and run `composer install`.
3. Open the file `config/services.yaml` and enter the url to your private recipes repo.
4. Setup the `APP_ENV` properly. This can be done in the `.env` file (create if needed) or on the hosting. Setting it to `prod` is recommended.
5. Run `php bin/console recipes:initialize` in order to download your recipes.

*Of course, you should deploy the project to where it will be hosted (probably before step 2).*

That's it, you should be up and running. You might want to [tweak the configuration](configuration.md) or to setup automatic recipe updates.

Installing a monitoring tool (like [Sentry](https://sentry.io)) is recommended.

### Using the server

Using the server in your Symfony project is as easy as adding the proper endpoint to your `composer.json`:

    {
        ...
        "symfony": {
            "endpoint": "https://your.domain.com"
        }
    }

#### Running the server locally

Running the server locally is possible by using symfony's server command: `php bin/console server:start` (oder `server:run` for a temporary instance).
Make sure to allow connections over http to composer (if using localhost) by adding this to the project's `composer.json`:

    {
        ...
        "config": {
            "secure-http": "false"
        }
    }
    
*It is not recommended to run the server locally. The best setup would be a private server behind a firewall.*