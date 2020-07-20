# Setup

Setting up the server should be quick and easy. These are the necessary steps:

1. [Download](https://github.com/moay/server-for-symfony-flex/releases) the project from Github (or `git clone https://github.com/moay/server-for-symfony-flex`)
2. Navigate to the project folder and run `composer install`.
3. Install Encore with `yarn install` (or with `docker run --rm --name=node --mount type=bind,source="$(pwd)"/,target=/app --workdir=/app node yarn yarn install`)
4. Build assets with `yarn encore dev` (or with `docker run --rm --name=node --mount type=bind,source="$(pwd)"/,target=/app --workdir=/app node yarn encore dev`)
5. Open the file `config/parameters.yaml` and enter the url to your private recipes repo (or provide appropriate environment variables).
6. Setup the `APP_ENV` properly. This can be done in the `.env` file (create if needed) or on the hosting. Setting it to `prod` is recommended.
7. Run `php bin/console recipes:initialize` in order to download your recipes.

*Of course, you should deploy the project to where it will be hosted (probably before step 2).*

That's it, you should be up and running. You might want to [tweak the configuration](configuration.md) or to setup automatic recipe updates.

Installing a monitoring tool (like [Sentry](https://sentry.io)) is recommended.

### Using the server

To enable recipes defined in your server, run the following command in your project:

```sh
composer config extra.symfony.endpoint https://your.domain.com
```

#### Running the server locally

Running the server locally is possible by using symfony's server command: `php bin/console server:start` (oder `server:run` for a temporary instance).
Make sure to allow connections over http to composer (if using localhost) running the following command:

```sh
composer config secure-http false
```

*It is not recommended to run the server locally. The best setup would be a private server behind a firewall.*
