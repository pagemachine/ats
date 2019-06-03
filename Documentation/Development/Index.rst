.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _development-manual:

Development
===========

.. only:: html

   This chapter provides information for developers (signals, extendable features etc.)


Signals
-------

The following signals are provided so you can extend ATS behaviour. Signals are still WIP and added as we need them for our projects.
Need a signal somewhere in the code? Feel free to contact us.

PAGEmachine\Ats\Message\MessageFactory: "afterMessageCreated"
*************************************************************

This signal allows for manipulation of mail messages before they are passed to the backend forms ("Invite", "Acknowledge"...). You can use it to manipulate messages and set default values, for example the CC and BCC recipients.
