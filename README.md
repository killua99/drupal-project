# Fork of Composer template for Drupal projects

This repository aim to add small tweaks for the Composer Template that are not
require for the common of the Drupal Projects. This tweaks are especific
settings that might be not interesting for the 99.99% of Drupal Projects.

I'll keep adding the features from the original template plus adding extra
tweaks.

This project template should provide a kickstart for managing your site
dependencies with [Composer](https://getcomposer.org/).

If you want to know how to use it as replacement for
[Drush Make](https://github.com/drush-ops/drush/blob/master/docs/make.md) visit
the [Documentation on drupal.org](https://www.drupal.org/node/2471553).

## Usage

First you need to [install composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

> Note: The instructions below refer to the [global composer installation](https://getcomposer.org/doc/00-intro.md#globally).
You might need to replace `composer` with `php composer.phar` (or similar) for your setup.

I recommend to install globaly this component for composer to download parallel. [hirak/prestissimo](https://github.com/hirak/prestissimo)

After that you can create the project:

```
composer create-project killua99/drupal-project-tweaks awesome-project --no-progress --profile --prefer-dist
```

With `composer require ...` you can download new dependencies to your installation.

```
cd awesome-project
composer require drupal/devel
```

## What does the template do?

When installing the given `composer.json` some tasks are taken care of:

* Drupal will be installed in the `web`-directory.
* Autoloader is implemented to use the generated composer autoloader in `vendor/autoload.php`,
  instead of the one provided by Drupal (`web/vendor/autoload.php`).
* Modules (packages of type `drupal-module`) will be placed in `web/modules/contrib/`
* Theme (packages of type `drupal-theme`) will be placed in `web/themes/contrib/`
* Profiles (packages of type `drupal-profile`) will be placed in `web/profiles/contrib/`
* Creates default writable versions of `settings.php` and `services.yml`.
* Creates `static`-directory.
* Latest version of drush is installed locally for use at `vendor/bin/drush`.
* Latest version of DrupalConsole is installed locally for use at `vendor/bin/drupal`.
* Write a public_html to increase the security of Drupal see [Moving to docroot](https://www.drupal.org/node/2767907) I call it public_html (I think makes more sense).

## Updating Drupal Core

I'm thinking how to keep it simple, for now I just run this command.

    composer update --prefer-dist -o


## Generate composer.json from existing project

With using [the "Composer Generate" drush extension](https://www.drupal.org/project/composer_generate)
you can now generate a basic `composer.json` file from an existing project. Note
that the generated `composer.json` might differ from this project's file.


## FAQ

### Should I commit the contrib modules I download

Composer recommends **no**. They provide [argumentation against but also workrounds if a project decides to do it anyway](https://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).

### How can I apply patches to downloaded modules?

If you need to apply patches (depending on the project being modified, a pull request is often a better solution), you can do so with the [composer-patches](https://github.com/cweagans/composer-patches) plugin.

To add a patch to drupal module foobar insert the patches section in the extra section of composer.json:
```json
"extra": {
    "patches": {
        "drupal/foobar": {
            "Patch description": "URL to patch"
        }
    }
}
```
