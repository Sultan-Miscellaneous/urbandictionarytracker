<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Definition extends CI_Model {
	
	public $user_id;
	public $definition_id; //auto incremented
	public $urbandictionary_id;
	public $word;
	public $example;
	public $meaning;
	public $current_upvotes;
	public $current_downvotes;
	public $lastupdated; //auto updated by mysql server

	public function __construct(){
		$this->load->database();
		$this->load->library('table');
		parent::__construct();
	}

	public function setDefinitionProperties($_user_id,$_urbandictionary_id,$_word, $_meaning, $_example, $_current_upvotes, $_current_downvotes){
		$this->user_id = $_user_id;
		$this->urbandictionary_id = $_urbandictionary_id;
		$this->word = $_word;
		$this->meaning = $_meaning;
		$this->example = $_example;
		$this->current_upvotes = $_current_upvotes;
		$this->current_downvotes = $_current_downvotes;
	}

	public function checkIfDefinitionExists(){
		$query = $this->db->Select('urbandictionary_id')->From('user_definitions')->where('urbandictionary_id',$this->urbandictionary_id)->get();
		return $query->num_rows()>=1;
	}

	public function saveCurrentDefinitionToDatabase(){
		return $this->db->insert('user_definitions',$this);
	}

	public function getDefinitionsForUserWithId($id){
		$query = $this->db->Select('urbandictionary_id,word,example,meaning,current_upvotes,current_downvotes,lastupdated')->From('user_definitions')->where('user_id',$id)->get();
		$this->table->set_heading("Urbandictionary ID","Word","Example","Meaning","Upvotes","Downvotes","Last Updated");
		return $this->table->generate($query);
	}

}