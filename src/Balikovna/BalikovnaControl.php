<?php declare(strict_types=1);
/**
 * Author: Ivo Toman
 */

namespace Unio\Posta\Balikovna;


use Nette\Application\UI;
use Nette\Localization\ITranslator;

class BalikovnaControl extends UI\Control
{
	/**
	 * @var BalikovnaRepository
	 */
	private $balikovnaRepository;
	/**
	 * @var ITranslator
	 */
	private $translator;

	/**
	 * @var string
	 */
	private $latteFile = "BalikovnaControl.latte";

	/**
	 * @var array
	 */
	private $balikovny = [];

	/**
	 * @var callable[]
	 * function (IShipBox $balikovna); Occurs when the balikovna is selected
	 */
	public $onSelect = [];


	public function __construct(BalikovnaRepository $balikovnaRepository, ITranslator  $translator)
	{
		$this->balikovnaRepository = $balikovnaRepository;
		$this->translator = $translator;
	}

	protected function createComponentForm(): UI\Form
	{
		$form = new UI\Form;
		$form->setTranslator($this->translator);
		$form->getElementPrototype()
			->class("ajax");
		$form->addText("query", "Town");

		$form->addSubmit("send", "Search");
		$form->onSuccess[] = [$this, 'processForm'];
		return $form;
	}

	public function processForm(UI\Form $form): void
	{
		$val = $form->getValues();

		if ($val->query != '') {
			$entries = $this->balikovnaRepository->search($val->query);
		}

		if (!isset($entries) || count($entries) === 0) {
			$this->flashMessage("Zadaným kritériím nevyhovuje žádná balíkovna, změňte prosím Vámi zadaná kritéria vyhledávání.", "error");
		} else {
			$this->balikovny = $entries;
		}

		$this->redrawControl();
	}

	public function render(): void
	{
		$this->template->setFile(__DIR__ . "/" . $this->latteFile);
		$this->template->balikovny = $this->balikovny;
		$this->template->img = "/assets/components/posta/images";
		$this->template->render();
	}

	public function handleSelect($id)
	{
		$balikovna = $this->balikovnaRepository->getBoxById($id);
		$this->flashMessage(sprintf("Balíkovna %s vybrána", $balikovna->getName()), "ok");
		$this->onSelect($balikovna);
		$this->redrawControl();
	}
}
