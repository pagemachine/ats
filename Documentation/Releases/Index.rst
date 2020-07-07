.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _releases-manual:

Releases
========

2.0.0
-------

-------
Breaking: Extconf department job configuration format change
-------

The configuration for the job department now has to be formated as array instead of an array.

.. code-block:: php

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['job']['roles'] = [
        'department' => ["Abt", "Ausbilder", "Ausbildung", "FB"]
    ];

Previously the array (for the same case) looked like:

.. code-block:: php

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['job']['roles'] = [
        'department' => '"Abt", "Ausbilder", "Ausbildung", "FB"'
    ];

