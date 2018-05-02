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


