<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Service\ExtconfService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Resolves markers like [[firstname]] from mail/pdf-forms
 */
class MarkerService implements SingletonInterface {

	const CONTEXT_DEFAULT = 'default';
	const CONTEXT_MAIL = 'mail';
	const CONTEXT_PDF = 'pdf';

	/**
	 *
	 * @var ExtconfService
	 */
	protected $extconfService;

	/**
	 * Input marker format from RTE
	 *
	 * @var string
	 */
	public $inputFormat = "[[%s]]";

	/**
	 *
	 * @param ExtconfService|null $extconfService
	 */
	public function __construct(ExtconfService $extconfService = null) {

		$this->extconfService = $extconfService ?: GeneralUtility::makeInstance(ExtconfService::class);

	}


	/**
	 * Replaces all found markers in a given text and returns it
	 *
	 * @param  string      $source
     * @param  string      $context
	 * @return string
	 */
	public function replaceMarkers($source, $context = MarkerService::CONTEXT_DEFAULT) {

		$source = $this->replaceContextRelatedMarkers($source, $context);
		$source = $this->convertToFluidMarkers($source);

        return $source;

	}

	/**
	 * Checks if there are context related replacements (like signature => mail_signature for mail) and replaces them
     *
     * @param  string      $source
     * @param  string      $context
     * @return string
     */
	protected function replaceContextRelatedMarkers($source, $context) {

		$contextReplacements = $this->extconfService->getMarkerReplacements($context);

		$search = [];
		$replace = [];

		if (!empty($contextReplacements)) {
			foreach ($contextReplacements as $searchText => $replaceText) {

				$search[] = sprintf($this->inputFormat, $searchText);
				$replace[] = sprintf($this->inputFormat, $replaceText);
			}
		}

		$source = str_replace($search, $replace, $source);

		return $source;

	}

	/**
	 * Converts markers to fluid markers
     *
     * @param  string      $source
     * @return string
     */
	protected function convertToFluidMarkers($source) {
		list($startString, $endString) = explode("%s", $this->inputFormat);

		$source = str_replace([$startString, $endString], ["{", " -> f:format.raw()}"], $source);


		return $source;

	}


}
