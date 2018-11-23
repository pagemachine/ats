module.tx_ats.settings.anonymization {
  minimumAge = 90 days
  objects {
    PAGEmachine\Ats\Domain\Model\Application {
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
    }
  }
}
