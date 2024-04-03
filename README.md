# Applicant Tracking System (ATS) ![CI](https://github.com/pagemachine/ats/workflows/CI/badge.svg)

Highly customizable enterprise application tracking system based on Extbase & Fluid. Provides management of job offers and job applications, allowing for complex job application workflows involving numerous roles as they are required in environments of universities as well as private and public companies.

Make sure to read the [Extension Manual](https://docs.typo3.org/typo3cms/extensions/ats/) for details.

## Installation

This extension is installable from various sources:

1. Via [Composer](https://packagist.org/packages/pagemachine/ats):

        composer require pagemachine/ats

2. From the [TYPO3 Extension Repository](https://extensions.typo3.org/extension/ats/)
3. From [Github](https://github.com/pagemachine/ats/releases)

## Testing

All tests can be executed with the shipped Docker Compose definition:

    docker compose run --rm app composer build
