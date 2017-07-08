<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 
*/
class Homepage_controller extends CI_Controller {
	
	public function __construct() {
		parent::__construct();

		// Load form helper library
		$this->load->helper('form');

		// Load form validation library
		$this->load->library('form_validation');

		// Load session library
		$this->load->library('session');

		// Load Definitions model
		$this->load->model('Definition');

		$this->load->helper('url');

	}
	public function loadViewWithMessage($view,$message){
		$data = array('message' => $message);
		$this->load->view($view,$data);
	}

	// Show login page
	public function index() {
		if ($this->session->userdata('user_name') !== NULL) {
			$data = $this->session->userdata();
			$data['definitions'] = $this->Definition->getDefinitionsForUserWithId($this->session->userdata('id'));
			$this->load->view('homepage',$data);
		}else{
			$this->loadViewWithMessage('login_form','Please login<br>');
		}
	}

	public function addDefinitionLink(){
		$this->form_validation->set_rules('link',"Link","required|callback_checkUrl|trim");
		if ($this->form_validation->run() == FALSE) //failed
		{
			$this->index();
		}else{
			$link = $this->input->post('link');
			$this->beginHtmlParseProcessAt($link);
		}
	}

	public function checkUrl(&$str){
		$originalString = $str;
		$checkLinkResult = $this->checkAndPrepLink($str);
	    if ($checkLinkResult === False)
	    {
	        $this->form_validation->set_message('checkUrl', "The word {$originalString} is not a valid urbandictionary url, nor word");
	        return FALSE;
	    }
	    else
	    {	
	        $html = file_get_contents($str);
			$DOM = new DOMDocument;
			//DOM DOESN'T WORK WITH HTML5 SUPRESSING ALL WARNINGS
   			@$DOM->loadHTML($html);
   			$content = $DOM->getElementById('content')->nodeValue; //go to content node
   			if (strpos($content,"There aren't any definitions for") !== false) {
   				$this->form_validation->set_message('checkUrl', "There are no definitions for the {$originalString} on urbandictionary.com");
   				return FALSE;
   			}else{
				return true;
   			}
	    }
	}
	//url sent here MUST BE prepped for html download, and have no Pages Get request
	public function beginHtmlParseProcessAt($baseLink){
		//check if there are multiple pages
		$html = file_get_contents($baseLink);
		//get word from base link (I know it's contrived)
		$original_search_word = urldecode(substr($baseLink,strpos($baseLink, '=')+1)); //no pages variable in url, so only one occurence of =
		$original_search_word = rawurlencode($original_search_word);
		$DOM = new DOMDocument;
		//DOM DOESN'T WORK WITH HTML5 SUPRESSING ALL WARNINGS
   		@$DOM->loadHTML($html);
   		$content = $DOM->getElementById('content'); //go to content node
   		//hack to get max number of pages out of raw html.. You will forget how you did this so check urban dictionary html of a word that has many definitions (multiple pages) and look in content to see what the regex below matches with
   		preg_match_all("~\/define\.php\?term={$original_search_word}&amp;page=(\d+)~", $content->C14N(), $matches, PREG_PATTERN_ORDER);
   		$numberOfPages = 1;
   		if (!empty($matches[1])) {
   			$numberOfPages = max($matches[1]);
   		}
   		for ($i=1; $i <= $numberOfPages ; $i++) { 
   			$link = $baseLink.'&page='.$i;
   			$this->parseHtmlAt($link,$original_search_word);
   		}
	}

	public function checkAndPrepLink(&$link){ //pass link by reference to modify it
		//if user input is url but needs to be prepped
		$regExResult = preg_match("~^((http:\/\/www.)|(www.)|(http:\/\/))?urbandictionary.com\/define\.php\?term=[\w+]+((&page=)\d)?$~", $link,$matches);
		if ($regExResult === 1) { //check if URL given has http 
			if (array_search('http://www.', $matches)===False && array_search('http://', $matches)===False) {
				$link = 'http://'.$link;
			}
			//check if there is a pages get request to remove it
		    if(array_search('&page=', $matches) !== False){
		    	$parts = explode("&", $link);
		    	$link = $parts[0];
		    }
		   	return True;
		}else{ //if user input is just a word, check if valid (only spaces allowed and alphanumeric characters only) if it is, then create valid url, otherwise return False
			$regExResult = preg_match('~^[\w ]+$~',$link);
			if ($regExResult === 1) {
				$link = urlencode($link);
				$link = "http://www.urbandictionary.com/define.php?term=".$link;
				return True;
			}else{
				return False;
			}
		}
			
	}

	public function parseHtmlAt($link){
		//awsa5 code katabto fi 7ayaty
		$html = file_get_contents($link);
		$DOM = new DOMDocument;
			//DOM DOESN'T WORK WITH HTML5 SUPRESSING ALL WARNINGS
		@$DOM->loadHTML($html);
		$DOM->preserveWhiteSpace = false;
   		$content = $DOM->getElementById('content'); //go to content node
		$urbandictionary_id = "";
		$word = "";
		$meaning = "";
		$example = "";
		$upvotes = "";
		$downvotes = "";
   		// echo $content->C14N();
   		foreach ($content->childNodes as $definition){ //get children (basically the definitions)
   			if (isset($definition->attributes)) { //weird rogue children that have no definitions
   				$class = $definition->attributes->getNamedItem('class')->value;
	   			if ($class === 'def-panel') {
	   				foreach ($definition->childNodes as $property){ //loop through children(properties) of definitions
						if (isset($property->attributes)) {
							//check class attribute of property
							$class = $property->attributes->getNamedItem('class')->value;
							switch ($class) { //check class to aquire data 
								case 'def-header': 
									//def header has word, use regex to get word out of <a> tag
									$rawHtml = $property->C14N(); 
									echo $rawHtml;
									preg_match('~<a class="word" href="\/define\.php\?term=([\w .+%]+)&?"? ?\w+;?=?\w+="?(\d+)"~', $rawHtml, $matches);
									$urbandictionary_id = $matches[2];
									$word = rawurldecode(urldecode($matches[1]));
									// echo "word: ".$matches[2].'<br>';  							  				
									break;

								case 'meaning':
									$meaning = $property->nodeValue;
									// echo "meaning: ".$property->nodeValue."<br>";

								break;

								case 'example':
									$example = $property->nodeValue;
									// echo "example: ".$property->nodeValue."<br>";

								break;

								case 'def-footer':
									$rawHtml = $property->C14N(); 
									if (preg_match_all('~<span class="count">(\w+)?<\/span>~', $rawHtml, $matches) === 0) {
										die('failed to extract upvote/downvoted count from definition');
									}
									$upvotes = $matches[1][0];
									$downvotes = $matches[1][1];
									// echo "upvotes: ".$matches[1][0]." downvotes: ".$matches[1][1]."<br>";
								break;

								default:
								break;
							}
						}
					}
					$aquiredDefinition = new $this->Definition;
					$aquiredDefinition->setDefinitionProperties($this->session->userdata('id'),$urbandictionary_id,$word, $meaning, $example, $upvotes, $downvotes);
					if($aquiredDefinition->checkIfDefinitionExists() === False){
						$aquiredDefinition->saveCurrentDefinitionToDatabase();
					}
				}
   			}
		}
		redirect('/Homepage_controller/index', 'refresh');
	}
	// Show registration page

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
}
