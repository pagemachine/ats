.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _security-manual:

Security
========

.. only:: html

	This chapter describes what the features and limitations of the ats extension


General Considerations
----------------------

Application data are personal data that have to be protected from unauthorized access.
Using the ats extension on a web server may impose some risks.
Therefore for enterprise use we strongly recommend to install the ats on two separate servers, each holding a TYPO3 installation. The web server is only used for the output of jobs at the frontend and for the appliation form.
A second server, which should be located in the intranet, is used to generate the job offers and process the applications by authorized backend users.
Both servers have to be kept in sync, taking care of deleting all data on the public TYPO3 instance as soon as it has been transferred to the internal server.

How to realize a 2-server solution
----------------------------------

There are several ways to perform such a 2-server installation and the required sync.
If you are unsure, if you can achieve this on your own or need a quick out-of-the-box solution, you may contact the authors of the ats extension for consultancy.
We have developed an extension which performs the sync process using a RabbitMQ service.

