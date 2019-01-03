module.tx_ats.settings.anonymization {
  objects {
    PAGEmachine\Ats\Domain\Model\Application {
      # Default anonymization setup and conditions. You can add your own setups if necessary.
      default {
        mode = anonymize
        # Anonymize all applications older than 90 days.
        minimumAge = 90 days
        conditions {
          status {
            property = status
            operator = greaterThanOrEqual
            value = 100
            type = int
          }
          unpooled {
            property = pool
            operator = equals
            value = 0
            type = int
          }
        }
        properties {
          title = *
          firstname = *
          surname = *
          nationality = *
          street = *
          zipcode = *
          city = *
          email = *
          phone = *
          mobile = *
          employed = 0
          schoolQualification = *
          professionalQualification = *
          professionalQualificationFinalGrade = *
          academicDegree = *
          academicDegreeFinalGrade = *
          doctoralDegree = *
          doctoralDegreeFinalGrade = *
          previousKnowledge = *
          itKnowledge = *
          comment = *
          referrer = *
        }
        children {
          history {
            mode = anonymize_and_delete
            properties {
              subject = *
              details = a:0:{}
              historyData = a:0:{}
              user = 0
            }
          }
          notes {
            mode = anonymize_and_delete
            properties {
              subject = *
              details = *
              is_internal = 0
              user = 0
            }
          }
          languageSkills {
            mode = anonymize_and_delete
            properties {
              level = 0
              language = 0
              textLanguage = *
            }
          }
          files {
            mode = delete_files
          }
        }
      }
    }
  }
}
