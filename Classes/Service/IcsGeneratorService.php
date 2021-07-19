<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\SingletonInterface;
use PAGEmachine\Ats\Message\VideoInvitationMessage;

class IcsGeneratorService implements SingletonInterface
{

  /**
  * DateTime format
  * @var string
  */
  const DATE_TIME_FORMAT = 'Ymd\THis';

  /* * @var string $description
   */
  protected $description;

  /* * @var string $dtend
   */
  protected $date;

  /* * @var string $dtend
   */
  protected $dtend;

  /* * @var string $dtstart
   */
  protected $dtstart;

  /* * @var string $summary
    */
  protected $summary;

  /* * @var string $url
   */
  protected $url;
 
  public function __construct(VideoInvitationMessage $message) 
  {
      $this->description = $message->getRenderedBody();
      $this->date = $message->getDate();
      $this->dtstart = $message->getAppointmentFrom();
      $this->dtend = $message->getAppointmentUntil();
      $this->summary = $message->getRenderedSubject();
      $this->url = $message->getUrl();
  }

  /**
  * @return \DateTime
  * @codeCoverageIgnore
  */
  private function convertToDateTime($date, $time) {
    $datetime = new \Datetime($date->format('Y-m-d').'T'.$time->format('H:i'));
    return $datetime;        
    }

  /**
  * @return string
  * @codeCoverageIgnore
  */
  public function icsProps_to_string() {
    $rows = $this->ics_properties();
    return implode("\r\n", $rows);
  }

  /**
  * @return array|string $ics_props
  * @codeCoverageIgnore
  */
  private function ics_properties() {
    $ics_props = array(
      'BEGIN:VCALENDAR',
      'VERSION:2.0',
      'PRODID:-//Pagemachine AG//Pagemachine ATS//DE',
      'CALSCALE:GREGORIAN',
      'BEGIN:VEVENT'
    );

    $props = array();

    $props['SUMMARY'] = $this->summary;
    $props['DTSTART'] = $this->format_timestamp( $this->convertToDateTime($this->date, $this->dtstart));
    $props['DTEND'] = $this->format_timestamp( $this->convertToDateTime($this->date, $this->dtend));
    $props['DTSTAMP'] = $this->format_timestamp(new \DateTime('now'));  
    $props['UID'] = uniqid();
    $props['DESCRIPTION'] = $this->description;
    $props['URL;VALUE=URI'] = $this->url;

    foreach ($props as $k => $v) {
      $ics_props[] = "$k:$v";
    }

    $ics_props[] = 'END:VEVENT';
    $ics_props[] = 'END:VCALENDAR';

    return $ics_props;
  }

  /**
  * @return string $timestamp
  * @codeCoverageIgnore
  */
  private function format_timestamp($timestamp) {
    return $timestamp->format(self::DATE_TIME_FORMAT);
  }

}
?>