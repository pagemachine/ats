<?php
namespace PAGEmachine\Ats\Controller\Application;

use DateTime;
use PAGEmachine\Ats\Domain\Model\ApplicationSimple;
use PAGEmachine\Ats\Domain\Model\FileReference;
use PAGEmachine\Ats\Domain\Repository\ApplicationSimpleRepository;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;

class SimpleFormController extends AbstractApplicationController
{
    /**
     * @var ApplicationSimpleRepository
     */
    protected $repository = null;

    /**
     * @param  ApplicationSimpleRepository
     */
    public function injectRepository(ApplicationSimpleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @var \TYPO3\CMS\Extbase\Security\Cryptography\HashService
     */
    protected $hashService;

    /**
     * @param \TYPO3\CMS\Extbase\Security\Cryptography\HashService $hashService
     */
    public function injectHashService(HashService $hashService)
    {
        $this->hashService = $hashService;
    }

    /**
     * Saves upload and forwards back to edit
     *
     * @param  ApplicationSimple $application
     * @return void
     */
    public function saveUploadAction(ApplicationSimple $application)
    {
        $this->repository->addOrUpdate($application);
        $this->redirect("simpleForm", null, null, ['application' => $application->getUid()]);
    }

    /**
     * @param  ApplicationSimple $application
     * @return void
     */
    public function removeUploadAction(ApplicationSimple $application, FileReference $file)
    {

        $application->removeFile($file);

        $this->repository->addOrUpdate($application);

        $this->redirect("simpleForm", null, null, ["application" => $application->getUid()]);
    }

    /**
     * @param  Job $job
     * @param  ApplicationSimple $application
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("application")
     * @ignorevalidation $application
     * @return void
     */
    public function simpleFormAction(ApplicationSimple $application)
    {
        $time = new DateTime();
        $hMac = $this->hashService->appendHmac((string)$application->getUid().';'.$time->getTimestamp());
        $this->view->assign("application", $application);
        $this->view->assign("hmac", $hMac);
    }

    /**
     *
     * @param  ApplicationSimple $application
     * @param  string $something
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */
    public function submitAction(ApplicationSimple $application, string $something)
    {
        
        try {
            $combinedString = $this->hashService->validateAndStripHmac($something);
            $splitString = explode(";", $combinedString);
            if (count($splitString) != 2) {
                throw new \TYPO3\CMS\Extbase\Security\Exception\InvalidHashException('Hmac string not long enough.', 1600344513);
            }
            if ($splitString[0] != (string)$application->getUid()) {
                throw new \TYPO3\CMS\Extbase\Security\Exception\InvalidHashException('Hmac application not correct.', 1600344514);
            }
            $time = new DateTime();
            $timeDiff = $time->getTimestamp() - $splitString[1];
            if ($timeDiff < 3) {
                throw new \TYPO3\CMS\Extbase\Security\Exception\InvalidHashException('Application was filled in too fast. '.$timeDiff.'s', 1600344515);
            }
            if (!empty($application->getZipcode())) {
                throw new \TYPO3\CMS\Extbase\Security\Exception\InvalidHashException('Honeypot field was filled out.', 1600344516);
            }
        } catch (\TYPO3\CMS\Extbase\Security\Exception\InvalidHashException $e) {
            $this->redirect("simpleForm", null, null, ["application" => $application->getUid()]);
        }
        $this->repository->addOrUpdate($application);
        $this->redirect("showSimpleSummary", "Application\\Submit", null, ['application' => $application->getUid()]);
    }
}
