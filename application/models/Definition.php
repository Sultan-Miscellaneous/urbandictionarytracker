<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Definition extends CI_Model {
	
	public $word;
	public $id;
	public $meaning;
	public $example;
	public $upvotes;
	public $downvotes;
	public $timestamp;

	public function __construct(){
		parent::__construct();
	}

	public function setDefinitionProperties($_word, $_meaning, $_example, $_upvotes, $_downvotes,$_timestamp){
		$word = $_word;
		$meaning = $_meaning;
		$example = $_example;
		$upvotes = $_upvotes;
		$downvotes = $_downvotes;
		$timestamp = $_timestamp;
	}

	public function saveToDatabaseWithLinkId($id){

	}

}