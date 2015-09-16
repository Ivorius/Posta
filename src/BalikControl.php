<?php

/**
 * Balík na poštu - ČP
 *
 * @author Ivo
 */

namespace Unio\Posta;

use Nette\Application\UI;

class BalikControl extends UI\Control {

    /**
     * @var IPostManager
     */
    private $postManager;
    
    /** 
     * limit pro dobírku (některé pošty naumožňují ukládat s vyšší částkou)     *
     * @var numeric
     */
    private $limit = 50000;
    
    /**
     * hodnota dobírky / košíku
     * @var numerice
     */
    private $value;
    
    
    private $posts = array();
    
     /** 
      * @var callable[]  
      * function ($posta); Occurs when the post is selected 
      */
    public $onSelect = array();

    public function __construct(IPostManager $postManager ){
	$this->postManager = $postManager;
	$this->template->css = "/assets/www/posta/css";
	$this->template->js = "/assets/www/posta/js";
	$this->template->img = "/assets/www/posta/images";
	$this->template->limit = $this->limit;	
	$this->template->posts = $this->posts;
    }

    public function render() {
	$this->template->setFile(__DIR__ . "/BalikControl.latte");

	$this->template->value = $this->value;
	$this->template->render();
    }
    
     public function renderHead() {
	$this->template->setFile(__DIR__ . "/BalikControlHead.latte");
	$this->template->render();
    }

    protected function createComponentForm() {
	$form = new UI\Form;
	$form->getElementPrototype()
		->class("ajax");
	$form->addText("town", "Město");
	$form->addText("postcode", "PSČ")
		->setAttribute('class', 'posta_psc');
	$form->addSubmit("send", "Najít poštu");
	$form->onSuccess[] = $this->processForm;
	return $form;
    }

    public function processForm(UI\Form $form) {
	$val = $form->getValues();

	if ($val->postcode != '') {
	    $psc = intval(str_replace(' ', '', $val->postcode));
	    $posts = $this->postManager->findByPostCode($psc);
	} elseif ($val->town != '') {
	    $posts = $this->postManager->findByTown($val->town);
	} 
	
	if(count($posts) == 0) {
	    $this->flashMessage("Zadaným kritériím nevyhovuje žádná pošta, změňte prosím Vámi zadaná kritéria vyhledávání.", "error");
	}
	
	$this->template->posts = $posts;
	
	if($this->presenter->isAjax) {
	   $this->invalidateControl();
	} else {
	    $this->redirect("this");
	}
    }
    
    /**
     * Při výběru pošty
     * 
     * @param int $id pošty
     * 
     */
    public function handleSelect($id) {	
	$posta = $this->postManager->get($id);
	$this->onSelect($posta);
	$this->invalidateControl();
    }
    
    
    public function setValue($value) {
	$this->value = $value;
    }

}