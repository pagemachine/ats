<?php
namespace PAGEmachine\Ats\ViewHelpers\Form;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Extended select view helper which allows for translation of option values,
 * useful e.g. for rendering enumeration values
 */
class TranslatableSelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{
  /**
   * @return void
   */
    public function initializeArguments()
    {

        parent::initializeArguments();

        $this->registerArgument('translationPrefix', 'string', 'Prefixed to the field value to build the translation identifier.', true, '');
        $this->registerArgument('extensionName', 'string', 'Used to choose a language set from a different extension than the current.', false, '');
        $this->registerArgument('appendOptionLabel', 'string', 'If specified, will provide an option at last position with the specified label.');
        $this->registerArgument('appendOptionValue', 'string', 'If specified, will provide an option at last position with the specified value.');
    }

  /**
   * Render one option tag
   *
   * @param string $value value attribute of the option tag (will be escaped)
   * @param string $label content of the option tag (will be escaped)
   * @param bool $isSelected specifies wheter or not to add selected attribute
   * @return string the rendered option tag
   */
    protected function renderOptionTag($value, $label, $isSelected)
    {
        $output = '<option value="' . htmlspecialchars($value) . '"';

        if ($isSelected) {
            $output .= ' selected="selected"';
        }

        $translationId = $this->arguments['translationPrefix'] . $value;
        $request = $this->controllerContext->getRequest();
        $extensionName = $this->arguments['extensionName'] ?: $request->getControllerExtensionName();
        $translatedLabel = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($translationId, $extensionName);

        if ($translatedLabel !== null) {
            $label = $translatedLabel;
        }

        $output .= '>' . htmlspecialchars($label) . '</option>';

        return $output;
    }

  /**
   * Render the option tags.
   *
   * @param array $options the options for the form.
   * @return string rendered tags.
   */
    protected function renderOptionTags($options)
    {
        $output = parent::renderOptionTags($options);

        if ($this->hasArgument('appendOptionLabel')) {
            $value = $this->hasArgument('appendOptionValue') ? $this->arguments['appendOptionValue'] : '';
            $isSelected = $this->isSelected($value);
            $label = $this->arguments['appendOptionLabel'];
            $output .= $this->renderOptionTag($value, $label, $isSelected) . chr(10);
        }

        return $output;
    }
}
