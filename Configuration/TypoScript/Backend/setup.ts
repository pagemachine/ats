module.tx_ats {
    settings {
        deadlineTime = 1209600
        dateFormat = d.m.Y
        timeFormat = H:i
    }
    persistence {
        storagePid = {$module.tx_ats.persistence.storagePid}

        classes {
            PAGEmachine\Ats\Domain\Model\Job {
              mapping {
                  columns {
                    crdate.mapOnProperty = creationDate
                  }
              }
            }
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
    view {
        templateRootPaths.0 = {$module.tx_ats.view.templateRootPath}
        partialRootPaths.0 = {$module.tx_ats.view.partialRootPath}
        layoutRootPaths.0 = {$module.tx_ats.view.layoutRootPath}
    }
    features {
        ignoreAllEnableFieldsInBe = true
    }
}

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
