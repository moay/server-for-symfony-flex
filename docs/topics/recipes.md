# Recipes

The server is a pure delivery system and won't actually interact with or change your recipes.

Nevertheless, there are some thoughts on recipes and how they are handled that you should keep in mind.

### Package resolving

[Symfony Flex](https://github.com/symfony/flex) will ask the server for a set of packages which are installed by composer. The server will then try to resolve this set of packages and answer by providing the proper recipes for the request.

Recipes are basically a set of files and instructions. The most important file of a recipe is the manifest file which should contain all instructions related to the recipe. You can learn more about how recipes are built in the [official repo](https://github.com/symfony/recipes).

Once you have setup the server, it will follow some basic rules when resolving the packages:

1) **Provide the newest version.** If you request a package at version 1.3 and you have a local recipe for version 1.0 and up, but the official endpoint provides version 1.2 and up, the latter will be used.

2) **Local recipes first.** If both a local recipe repo and the official endpoint have the same version, use the local recipe.

3) **Private over official.** If there are more than one local recipe repos, same version recipes will be selected in the order `private` > `official` > `contrib`.

There currently is no way to mix up recipes. If you want to have a customized or extended version of an official recipe, you'll have to provide a complete copy. We are planning on setting up a way to create recipe extension which will allow to extend official recipes and live until the next major version.

### Invalid manifest files

The entire system is based on the belief that you are capable of providing a valid json file. If your `manifest.json` is invalid, the server will display a warning label.

![broken manifest](https://user-images.githubusercontent.com/3605512/36715244-ae810436-1b95-11e8-842c-6ca9d7f29723.png)
