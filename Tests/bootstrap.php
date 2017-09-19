<?php

// Register composer autoloader
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
  throw new \RuntimeException('Could not find vendor/autoload.php, make sure you ran composer.');
}

define('PATH_thisScript', realpath(__DIR__ . '/../vendor/typo3/cms/typo3/index.php'));
define('TYPO3_MODE', 'BE');
putenv('TYPO3_CONTEXT=Testing');

call_user_func(function($composerClassLoader, $bootstrap) {
  // Use old setup order for TYPO3 < 7.3
  if (method_exists($bootstrap, 'unregisterClassLoader')) {
    $bootstrap->baseSetup('typo3/');
    $bootstrap->initializeClassLoader();
  } else {
    $bootstrap->initializeClassLoader($composerClassLoader);
    $bootstrap->baseSetup('typo3/');
  }

  // Backwards compatibility with TYPO3 < 7.3
  if (method_exists($bootstrap, 'disableCoreAndClassesCache')) {
    $bootstrap->disableCoreAndClassesCache();
  } else {
    $bootstrap->disableCoreCache();
  }
}, require_once __DIR__ . '/../vendor/autoload.php', \TYPO3\CMS\Core\Core\Bootstrap::getInstance());
