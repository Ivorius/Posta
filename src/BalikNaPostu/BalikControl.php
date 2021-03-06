<?php

/**
 * Balík na poštu - Česká a Slovenská pošta
 *
 * @author Ivo Toman
 * @version 0.2
 */

namespace Unio\Posta\BalikNaPostu;

use Nette\Application\UI;
use Nette\Localization\ITranslator;
use Unio\Posta\IRepository;

class BalikControl extends UI\Control
{

	/**
	 * @var IPostManager
	 */
	private $repository;

	/**
	 * @var ITranslator
	 */
	private $translator;

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

	/**
	 * @var string
	 */
	private $latteFile = "BalikControl.latte";


	private $posts = array();

	/**
	 * @var callable[]
	 * function (IShipBox $posta); Occurs when the post is selected
	 */
	public $onSelect = array();

	public function __construct(BalikRepository $repository, ITranslator $translator)
	{
		$this->repository = $repository;
		$this->translator = $translator;
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . "/" . $this->latteFile);
		$this->template->img = "/assets/components/posta/images";
		$this->template->limit = $this->limit;
		$this->template->posts = $this->posts;
		$this->template->value = $this->value;
		$this->template->render();
	}



	public function renderHead()
	{
		$this->template->setFile(__DIR__ . "/BalikControlHead.latte");
		$this->template->css = "/assets/components/posta/css";
		$this->template->js = "/assets/components/posta/js";
		$this->template->render();
	}

	protected function createComponentForm()
	{
		$form = new UI\Form;
		$form->setTranslator($this->translator);
		$form->getElementPrototype()
			->class("ajax");
		$form->addText("query", "Town");
		$form->addSubmit("send", "Search Post Office");
		$form->onSuccess[] = [$this, 'processForm'];
		return $form;
	}

	public function processForm(UI\Form $form)
	{
		$val = $form->getValues();

		if ($val->query != '') {
			$entries = $this->repository->search($val->query);
		}

		if (!isset($entries) || count($entries) === 0) {
			$this->flashMessage("Zadaným kritériím nevyhovuje žádná pošta, změňte prosím Vámi zadaná kritéria vyhledávání.", "error");
		} else {
			$this->posts = $entries;
		}

		if ($this->presenter->isAjax()) {
			$this->redrawControl();
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
	public function handleSelect($id)
	{
		$posta = $this->repository->getBoxById($id);
		$this->flashMessage(sprintf("Pošta %s vybrána",$posta->getName()), "ok");
		$this->onSelect($posta);
		$this->redrawControl();
	}


	public function setValue($value)
	{
		$this->value = $value;
	}

	public function setLatteFile($file) {
		$this->latteFile = $file;
	}

}
