page.includeJSFooter {
    ats_languages = EXT:ats/Resources/Public/JavaScript/LanguageFields.js
    ats_main = EXT:ats/Resources/Public/JavaScript/main.js
}

# Include JQuery if activated
[{$plugin.tx_ats.settings.includeJQuery} == '1']
page.includeJSFooterlibs.jQuery = https://code.jquery.com/jquery-2.2.4.min.js
page.includeJSFooterlibs.jQuery.external = 1
[global]

plugin.tx_ats {
    view {
        templateRootPaths.0 = {$plugin.tx_ats.view.templateRootPath}
        partialRootPaths.0 = {$plugin.tx_ats.view.partialRootPath}
        layoutRootPaths.0 = {$plugin.tx_ats.view.layoutRootPath}

        pluginNamespace = tx_ats
    }
    persistence {
        storagePid = {$plugin.tx_ats.persistence.storagePid}
    }
    settings {
        loginPage = {$plugin.tx_ats.settings.loginPage}
        applicationPage = {$plugin.tx_ats.settings.applicationPage}
        feUserGroup = {$plugin.tx_ats.settings.feUserGroup}
        policyPage = {$plugin.tx_ats.settings.policyPage}

        deadlineTime = 1209600
        dateFormat = d.m.Y
        timeFormat = H:i
        preferredListAction = listMine

        allowedStaticLanguages = {$plugin.tx_ats.settings.allowedStaticLanguages}

        ratingOptions {
          0 {
            name = NONE
            label = LLL:EXT:ats/Resources/Private/Language/locallang.xlf:tx_ats.application.rating.0
          }
          10 {
            name = UNSUITED
            label = LLL:EXT:ats/Resources/Private/Language/locallang.xlf:tx_ats.application.rating.10
          }
          20 {
            name = SUITED
            label = LLL:EXT:ats/Resources/Private/Language/locallang.xlf:tx_ats.application.rating.20
          }
          30 {
            name = SHORTLISTED
            label = LLL:EXT:ats/Resources/Private/Language/locallang.xlf:tx_ats.application.rating.30
          }
          40 {
            name = SELECTED
            label = LLL:EXT:ats/Resources/Private/Language/locallang.xlf:tx_ats.application.rating.40
          }
          50 {
            name = CANCELLED_BY_CANDIDATE
            label = LLL:EXT:ats/Resources/Private/Language/locallang.xlf:tx_ats.application.rating.50
          }
        }
    }
    persistence {
        classes {
            PAGEmachine\Ats\Domain\Model\Application {
                mapping {
                    columns {
                        crdate.mapOnProperty = creationDate
                    }
                }
            }
            PAGEmachine\Ats\Domain\Model\Note {
                mapping {
                    columns {
                        crdate.mapOnProperty = creationDate
                    }
                }
            }
            PAGEmachine\Ats\Domain\Model\History {
                mapping {
                    columns {
                        crdate.mapOnProperty = creationDate
                    }
                }
            }
            PAGEmachine\Ats\Domain\Model\AbstractApplication.mapping {
                tableName = tx_ats_domain_model_application
                recordType = PAGEmachine\Ats\Domain\Model\AbstractApplication
            }
            PAGEmachine\Ats\Domain\Model\ApplicationA.mapping {
                tableName = tx_ats_domain_model_application
                recordType = PAGEmachine\Ats\Domain\Model\AbstractApplication
            }
            PAGEmachine\Ats\Domain\Model\ApplicationB.mapping {
                tableName = tx_ats_domain_model_application
                recordType = PAGEmachine\Ats\Domain\Model\AbstractApplication
            }
            PAGEmachine\Ats\Domain\Model\ApplicationC.mapping {
                tableName = tx_ats_domain_model_application
                recordType = PAGEmachine\Ats\Domain\Model\AbstractApplication
            }
            PAGEmachine\Ats\Domain\Model\ApplicationD.mapping {
                tableName = tx_ats_domain_model_application
                recordType = PAGEmachine\Ats\Domain\Model\AbstractApplication
            }
            PAGEmachine\Ats\Domain\Model\ApplicationE.mapping {
                tableName = tx_ats_domain_model_application
                recordType = PAGEmachine\Ats\Domain\Model\AbstractApplication
            }
        }
    }
}

plugin.tx_ats_jobs < plugin.tx_ats

module.tx_ats {
    settings < plugin.tx_ats.settings
    persistence < plugin.tx_ats.persistence
    view < plugin.tx_ats.view
    features {
        ignoreAllEnableFieldsInBe = true
    }
}
<INCLUDE_TYPOSCRIPT: source="FILE:./anonymization.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:./cleanup.ts">

config.tx_extbase {
    persistence {
        classes {
            PAGEmachine\Ats\Domain\Model\FileReference {
                mapping {
                    tableName = sys_file_reference
                    columns {
                        uid_local.mapOnProperty = originalFileIdentifier
                    }
                }
            }
        }
    }
    objects {
        TYPO3\CMS\Extbase\Domain\Model\FileReference.className = PAGEmachine\Ats\Domain\Model\FileReference
        TYPO3\CMS\Extbase\Persistence\Generic\QueryFactoryInterface.className = PAGEmachine\Ats\Persistence\Generic\QueryFactory
    }
}
