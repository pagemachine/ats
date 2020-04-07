module.tx_ats.settings.anonymization {
  objects {
    PAGEmachine\Ats\Domain\Model\Application {
      # Default setup for fields and children, do not use by its own. Can be used as a template for custom setups.
      _default {
        mode = anonymize
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
          schoolQualification = 0
          professionalQualification = *
          professionalQualificationFinalGrade = *
          academicDegree = *
          academicDegreeFinalGrade = *
          doctoralDegree = *
          doctoralDegreeFinalGrade = *
          previousKnowledge = *
          itKnowledge = *
          comment = *
          referrer = 0
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

      # Default anonymization setup and conditions for archived applications.
      archived < ._default
      archived {
        minimumAge = 90 days
        ageProperty = creationDate
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
      }
      # Second default setup for pooled applications which should be kept longer (1 year by default)
      pooled < ._default
      pooled {
        minimumAge = 1 year
        ageProperty = creationDate
        conditions {
          status {
            property = status
            operator = greaterThanOrEqual
            value = 100
            type = int
          }
          pooled {
            property = pool
            operator = equals
            value = 1
            type = int
          }
        }
      }
    }
  }
}
