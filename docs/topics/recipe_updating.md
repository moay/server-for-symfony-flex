# Updating the recipes

You will need to update the recipes available to the server whenever your recipe repo is changed. There are two options for this:

### Update using commands

You can use the command `php bin/console recipes:update` to refresh the recipes. Read more in the commands section.

### Update using a webhook

In order to automate the process, a webhook is provided. You can setup Github (or Gitlab) to call this webhook whenever a push event occurs on your recipe repo.

Use the url `https://your.domain.com/webhook/update` to have the flex server update the repos. This will execute a `git pull` and a `git clean` on the local repo copy.