<?php
namespace PAGEmachine\Ats\Application\Note;

use TYPO3\CMS\Core\Type\Enumeration;
use TYPO3\CMS\Core\Type\Exception\InvalidEnumerationValueException;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class NoteSubject extends Enumeration
{
    const __default = self::NOTE;

    /**
     * The default subject - a simple note without any further action.
     * @var string
     */
    const NOTE = 'note';

    /**
     * Note appended to rating
     * @var string
     */
    const RATING = 'rating';

    /**
     * Note appended to personell rating
     * @var string
     */
    const RATINGPERSO = 'rating_perso';

    /**
     * Note appended to a status change
     * @var string
     */
    const STATUS = 'status';

    /**
     * Note appended to a clone
     * @var string
     */
    const CLONED = 'cloned';

    /**
     * Note added when application is returned to personell department
     * @var string
     */
    const BACKTOPERSO = 'back_to_perso';

    /**
     * Note added when application is closed
     * @var string
     */
    const CLOSED = 'closed';

    /**
     * Note added when application is moved from or to pool
     * @var string
     */
    const POOL = 'pool';

    /**
     * This subject is used for mappings coming from legacy implementations
     * which do not (yet) have a mapping. Prevents nasty errors.
     *
     * @var string
     */
    const UNKNOWN = 'unknown';

    /**
     * ATS legacy mapping to map old values to localizable values
     *
     * @TODO remove in V2
     *
     * @var array
     */
    protected static $atsLegacyMapping = [
        'Note' => 'note',
        'Rating' => 'rating',
        'Rating (Perso)' => 'rating_perso',
        'Status' => 'status',
        'Pool' => 'pool',
    ];

    /**
     * Jobmodul legacy mapping to map even older values to localizable values
     *
     * @TODO remove in V2
     *
     * @var array
     */
    protected static $jobmodulLegacyMapping = [
        'Bemerkung' => 'note',
        'Bewertung' => 'rating',
        'Workflow ändern' => 'status',
        'Bewerbung abschließen' => 'closed',
        'Bewerbung kopieren' => 'cloned',
    ];

    /**
     * Check and apply legacy mappings before construct
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        if (isset(static::$atsLegacyMapping[$value])) {
            $value = static::$atsLegacyMapping[$value];
        } elseif (isset(static::$jobmodulLegacyMapping[$value])) {
            $value = static::$jobmodulLegacyMapping[$value];
        }
        try {
            parent::__construct($value);
        } catch (InvalidEnumerationValueException $e) {
            $this->setValue(static::UNKNOWN);
        }
    }
}
