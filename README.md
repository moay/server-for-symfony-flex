# symfony-flex-server

[![Build Status](https://travis-ci.org/moay/symfony-flex-server.svg?branch=master)](https://travis-ci.org/moay/symfony-flex-server)

A self hosted server for Symfony Flex allowing private recipes, customized recipes and caching functionality for the official endpoints.

![ui](https://user-images.githubusercontent.com/3605512/36627099-eb239f48-193d-11e8-919a-d98003696d7b.png)

## Early stage

The server is basically ready to be used and is used in production, but there might still be some quirks. Any report on new issues is welcome. Thanks!
___

### Features

* private recipes: The server enables you to use your own recipes for your own packages, even with Satis or Private Packagist.
* seamless integration: The server integrates with the official symfony.sh endpoint and will serve the official recipes as well as your own ones.
* caching and mirroring: The server can easily be configured to mirror the official repos so that you are completely independent.

### Documentation

Full documentation is available here: [Documentation](https://symfony-flex-server.readthedocs.io)

### License

Published under the [MIT License](https://github.com/moay/symfony-flex-server/blob/master/LICENSE).
