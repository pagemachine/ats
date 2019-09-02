.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _security-manual:

Security
========

.. only:: html

	This chapter points out some security aspects regarding the ats extension.


General Considerations
----------------------

Application data is personal data that has to be protected from unauthorized access.

Using the ats extension on a web server may impose some risks.
Therefore for enterprise use we strongly recommend to install the ats on two separate servers, each holding a TYPO3 installation. The web server is only used for the output of jobs at the frontend and for the appliation form.

A second server, which should be located in the intranet, is used to generate the job offers and process the applications by authorized backend users.
Both servers have to be kept in sync, taking care of deleting all data on the public TYPO3 instance as soon as it has been transferred to the internal server.

How to realize a 2-server solution
----------------------------------

There are several ways to perform such a 2-server installation and the required sync.

If you are unsure, if you can achieve this on your own or need a quick out-of-the-box solution, you may contact the authors of the ats extension for consultancy.
We have developed an extension which performs the sync process using a RabbitMQ service.

Application file protection
---------------------------

When applying for a job, in most cases users will also upload files like resumes and certifications.
Since TYPO3 does not offer conditional access protection for publicly available asset folders (like fileadmin/), you need to do some steps yourself to protect those files:

1. Create a new folder on your webserver that is **outside** the document root (so no web users can access it). PHP (and TYPO3) should have proper read/write access to it. Ask your server administrator for help with this step if you cannot do this on your own.

2. Create a new **File Storage** record on root level of your pagetree. Set the base path to match the file path you just created. Note that the warning "storage is not public" is exactly what we want to see!

   .. image:: ../Images/Security/protected-storage.png
      :alt: Example protected storage

3. Go to the Extension Manager and open the ATS extension settings (gear symbol on the right side of the name). Move to the tab "advanced". Change the upload folder for application assets to the newly created storage ID (f.ex. "2:/"). You can also add a subfolder if desired, the folder must already exist inside the storage.

4. Save and clear all caches. ATS will now use the configured folder for new file uploads.

Since the folder is protected, TYPO3 uses a custom script ("DumpFile") to expose them to the public. ATS hooks into this script and if the requested file is within the ATS folder, it will only allow access for **logged-in backend users** and **the frontend user who created the application**.

GDPR-related features
---------------------

Personal data should not be kept longer than necessary on public servers. However, cleanup by hand is a lot of effort and hard deletions inside the database are not even possible via the TYPO3 interface.

This is why ATS provides tools in form of console/scheduler commands which do the cleanup and anonymization job for you.

 Anonymization
-------------

The command *applications:anonymize* is a script which anonymizes applications (fills them with an asterisk in all person-related fields) and deletes all relations and files associated to them.

**By default applications in closed status (>=100) which were created 90 days ago (not pooled) or 1 year ago (in pool) are subject to anonymization.**
They must be triggered f.ex. via scheduler, see below.

Application age is determined via creationDate, meaning: The moment the applicant started his/her application and finished the first page.

Creating the default scheduler tasks
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The default anonymization setup consists of two separate presets, one for archived applications and one for pooled applications.

You have to create a scheduler task for each of these presets. Step by step:

- Create a new scheduler task and select "Extbase CommandController Task" in the "Class" selectbox.
- Scroll down to "CommandController Command" and select "Ats Applications:anonymize".
- Set a frequency and a start time (recommended is once every night). Save the form.
- Scroll down again, now there is a new text field called "preset". Type in "archived". Save the form.

Now create another task for pooled applications, repeat all steps.
Set a slightly different start time so the tasks do not interfere. Enter "pooled" inside the "preset" field.

If your scheduler is correctly set up, it should now anonymize all old applications in the specified intervals.

Custom presets
^^^^^^^^^^^^^^^^^

**Note: The configuration part for anonymization has changed from version 1.12.1 to 1.13.0 (moving the configuration into preset sub-configuration). If your instance includes custom changes, you need to move them to the corresponding preset (archived).**

The default configuration presets can be found inside ``Configuration/TypoScript/Backend/anonymization.ts``.

You can also customize the exact behaviour for applications and their child records by creating your own preset.

- **mode** defines the exact anonymization behaviour: Either *anonymize*, *anonymize_and_delete* or *delete_files* for file references.
- **ageProperty** and **minimumAge** define the basic age condition. Read as "anonymize all applications where [ageProperty] is older than [minimumAge]".
- Inside **properties** you can define the replacement value for each property. Default is "*".
- If you want to keep a property or child as it is, simply remove the value or child section.

If you have custom conditions for anonymization, there is a subkey `conditions` inside the configuration for just that.
These conditions are appended to the general query. They use extbase query logic ("equals", "greaterThan"...).

**Example**: By default only applications with status 100 (employed) or higher are anonymized. Let's say you want to change this to 110 (cancelled) instead for the "archived" preset.

Inside your ``ext_typoscript_setup.txt``:
::
   module.tx_ats.settings.anonymization {
      objects {
         PAGEmachine\Ats\Domain\Model\Application {
            archived {
               ageProperty = creationDate
               minimumAge = 90 days
               conditions {
                 status {
                   property = status
                   operator = greaterThanOrEqual
                   value = 110
                   type = int
                 }
               }
            }
         }
      }
   }

Please note that the *type* option is not always necessary, but cleaner if the value is not a string.
If you want to pass on a boolean, use 0 or 1 and cast to "bool".

Also, the logic can only handle operators which require one value. Multivalued operators (in, between...) are currently not supported. Use multiple conditions for that.

Cleanup
-------

The commands *applications:cleanup* and *users:cleanup* triggers hard-deletion scripts which actually perform a database delete instead of just setting the deleted-flag - so the information is actually gone and cannot be hacked, sold, leaked by accident... you name it.

*users:cleanup* needs the sysfolder ID containing the users as parameter.

By default all unfinished applications are deleted **after 30 days** (measured by creation date, not last change). Users are deleted when their last login was **two years ago**. Both timespans are configurable via TypoScript (See :doc:`Configuration<../Configuration/Index>`, "Cleanup Settings").




