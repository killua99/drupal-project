<?php

namespace DrupalProject\composer;

use Composer\Script\Event;
use Composer\Semver\Comparator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class ScriptHandler
{
    protected static function getDrupalRoot($project_root)
    {
        return $project_root . '/web';
    }

    public static function createRequiredFiles(Event $event)
    {
        $fs = new Filesystem();
        $root = static::getDrupalRoot(getcwd());
        $files = '/public_html/static';

        $dirs = [
          getcwd() . '/config/sync',
          $root . '/modules',
          $root . '/profiles',
          $root . '/themes',
          $root . '/libraries',
          $root . '/public_html',
        ];

        $index_file = <<<EOD
<?php
/**
 *
 */
chdir('..');
require 'index.php';
EOD;

        try {
            $fs->mkdir($dirs);
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while creating your directory at " . $e->getPath());
        }

        try {
            $fs->copy($root . '/sites/default/default.settings.php', $root . '/sites/default/settings.php');
            try {
                $fs->chmod($root . '/sites/default/settings.php', 0666);
            } catch (IOExceptionInterface $e) {
                $event->getIO()->write("An error occurred while setting property for your directory at " . $e->getPath());
            }
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while copying your directory at " . $e->getPath());
        }

        try {
            $fs->copy($root . '/sites/default/default.services.yml', $root . '/sites/default/services.yml');
            try {
                $fs->chmod($root . '/sites/default/services.yml', 0666);
            } catch (IOExceptionInterface $e) {
                $event->getIO()->write("An error occurred while setting property for your directory at " . $e->getPath());
            }
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while copying your directory at " . $e->getPath());
        }

        try {
            $fs->mkdir($root . '/public_html/static', 0775);
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while creating your directory at " . $e->getPath());
        }

        try {
            $fs->dumpFile($root . '/public_html/index.php', $index_file);
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while creating your directory at " . $e->getPath());
        }

        try {
            $fs->symlink($root . '/robots.txt', $root . '/public_html/robots.txt', true);
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while creating symlink directory at " . $e->getPath());
        }

        try {
            $fs->symlink($root . '/.htaccess', $root . '/public_html/.htaccess', true);
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while creating symlink directory at " . $e->getPath());
        }

        try {
            $fs->symlink($root . '/core/modules/system/js', $root . '/public_html/core/modules/system/js', true);
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while creating symlink directory at " . $e->getPath());
        }

        try {
            $fs->symlink($root . '/libraries', $root . '/public_html/libraries', true);
        } catch (IOExceptionInterface $e) {
            $event->getIO()->write("An error occurred while creating symlink directory at " . $e->getPath());
        }
    }

    /**
     * Keep asset files in the right position.
     *
     * @todo: Finish the mirror asset.
     */
    public static function mirrorAssetFiles(Event $event)
    {
        $fs = new Filesystem();
        $root = static::getDrupalRoot(getcwd());
    }

  /**
   * Checks if the installed version of Composer is compatible.
   *
   * Composer 1.0.0 and higher consider a `composer install` without having a
   * lock file present as equal to `composer update`. We do not ship with a lock
   * file to avoid merge conflicts downstream, meaning that if a project is
   * installed with an older version of Composer the scaffolding of Drupal will
   * not be triggered. We check this here instead of in drupal-scaffold to be
   * able to give immediate feedback to the end user, rather than failing the
   * installation after going through the lengthy process of compiling and
   * downloading the Composer dependencies.
   *
   * @see https://github.com/composer/composer/pull/5035
   */
    public static function checkComposerVersion(Event $event)
    {
        $composer = $event->getComposer();
        $io = $event->getIO();
        $version = $composer::VERSION;

        // The dev-channel of composer uses the git revision as version number,
        // try to the branch alias instead.
        if (preg_match('/^[0-9a-f]{40}$/i', $version)) {
            $version = $composer::BRANCH_ALIAS_VERSION;
        }

        // If Composer is installed through git we have no easy way to determine if
        // it is new enough, just display a warning.
        if ($version === '@package_version@' || $version === '@package_branch_alias_version@') {
            $io->writeError('<warning>You are running a development version of Composer. If you experience problems, please update Composer to the latest stable version.</warning>');
        } elseif (Comparator::lessThan($version, '1.0.0')) {
            $io->writeError('<error>Drupal-project requires Composer version 1.0.0 or higher. Please update your Composer before continuing</error>.');
            exit(1);
        }
    }
}
