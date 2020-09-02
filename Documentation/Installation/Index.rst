.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _installation-manual:

Installation
============

.. only:: html

	This extension is installable from various sources:

1. Via Composer
---------------

::

   composer require pagemachine/ats

2. From the TYPO3 Extension Repository
--------------------------------------

https://extensions.typo3.org/extension/ats/

3. From Github
--------------

https://github.com/pagemachine/ats

Setup
-----

Prerequisites
^^^^^^^^^^^^^

Ensure your installation is ready for the ATS with the following steps:

   1. The extension needs a Frontend User login and registration setup so users can apply for a job.

      This task can be done in different ways. We recommend using `felogin` or `Hairu <https://extensions.typo3.org/extension/hairu/>`_ for the login form and `Femanager <https://extensions.typo3.org/extension/femanager/>`_ for registration.
   2. Create a storage folder and a jobs page in your pagetree, along with necessary login/registration pages
   3. Create a **Frontend Usergroup** for applicants inside the storage folder
   4. Create a page for your privacy policy text, if necessary

   You should now have a page setup similar to this:

   .. image:: ../Images/Installation/example_pagetree.png
      :alt: Example pagetree

   5. Create backend usergroups for personell and organizational departments. Assign a **location** to them (either *Headquarters* or *Branch office*):

   .. image:: ../Images/Installation/be_groups_location.png
      :alt: Assigning a location to backend usergroups

   6. Add these groups to the corresponding department Backend Users.


Extension Setup
^^^^^^^^^^^^^^^
   1. Include the static ATS template into your site root
   2. Add the necessary constants (this example matches the pagetree shown above):
   ::

      plugin.tx_ats {
        persistence {
          storagePid = 2
        }
        settings {
          policyPage = 6
          loginPage = 3
          feUserGroup = 1
        }
      }

   A full list of available constants can be found on the :doc:`Configuration page <../Configuration/Index>`.

   3. Add the **ATS Jobs** plugin to the jobs page
   4. Create jobs inside the storage folder.

   This extension also works without a login. For that leave the loginPage empty.

That's it, now the basics are set up and applicants can register for your jobs.

