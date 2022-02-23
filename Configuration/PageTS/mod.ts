mod {
  wizards.newContentElement.wizardItems.ats {
    header = Applicant Tracking System
    show = *

    elements {
      joblist {
        iconIdentifier = ats-joblist
        title = LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:plugin.jobs
        description = LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:plugin.jobs.description

        tt_content_defValues {
          CType = list
          list_type = ats_jobs
        }
      }
    }
  }
}
