.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _introduction-manual:

Introduction
=============

.. only:: html

	This chapter describes what the features and limitations of the ats extension


Applicant Tracking System (ATS)
-------------------------------

The Applicatn Tracking System is a highly customizable enterprise application tracking system based on Extbase & Fluid.
It provides management of job offers and job applications, allowing for complex job application workflows involving numerous roles as they are required in environments of universities as well as private and public companies.

Jobs
^^^^

Jobs may be created and edited by authorized backend users. By creating jobs in different sysfolders they may be seen in the frontend in different places, e.g. for different categories. They even may be distributed to different TYPO3 installations, if applicable.
For each job the authorized user may assign distinct users from the personal department and distinct organizational units who will be involved in the matching process for applications to this job.

Users & Roles
^^^^^^^^^^^^^

There are several roles that can be configured by creating backend user groups.
The two most important roles are:
- personal department
- organizational department
Other possible roles may be required according to german laws are:
- "Gleichstellungsbeauftragter"
- "Beauftragter für Schwerbehinderte Mitarbeiter"
- "Betriebsrat" (private companies) respectively "Personalrat" (public companies)

Communication with Applicants
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

During the application process the email address of applicants gets verified by a double opt in (DOI) process.
Thus later on the applicant may easily contacted out of the ats by email, supported by various text templates that may be created specificaly for the employer.
Alternatively all text templates may be converted to PDFs following the employers corporate design (with logo etc.) that may be printed for more formal communication via letter mail.

Application Form
^^^^^^^^^^^^^^^^

The application form consists of several tabs in order to improve usability by categorizing data.
Some data is mandatory, other data is optional. The upload of several documents like a CV ist possible. A login enables the applicant to complete his/her application in several sessions until it is perfect for submission.

