<?php
namespace PAGEmachine\Ats\Domain\Model;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * ApplicationD
 * @codeCoverageIgnore
 */
class ApplicationD extends ApplicationC {


	/**
	 * @var string $comment
	 */
	protected $comment;
	
	/**
	 * @return string
	 */
	public function getComment() {
	  return $this->comment;
	}
	
	/**
	 * @param string $comment
	 * @return void
	 */
	public function setComment($comment) {
	  $this->comment = $comment;
	}


	/**
	 * @var string $referrer
	 */
	protected $referrer;
	
	/**
	 * @return string
	 */
	public function getReferrer() {
	  return $this->referrer;
	}
	
	/**
	 * @param string $referrer
	 * @return void
	 */
	public function setReferrer($referrer) {
	  $this->referrer = $referrer;
	}


	/**
	 * @var integer $forwardToDepartments
	 * @validate NumberRange(minimum=1, maximum=2)
	 */
	protected $forwardToDepartments;
	
	/**
	 * @return integer
	 */
	public function getForwardToDepartments() {
	  return $this->forwardToDepartments;
	}
	
	/**
	 * @param integer $forwardToDepartments
	 * @return void
	 */
	public function setForwardToDepartments($forwardToDepartments) {
	  $this->forwardToDepartments = $forwardToDepartments;
	}



}