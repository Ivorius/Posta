# Balík na poštu
Komponenta pro výběr pošty k doručení balíku.

Instalace:

1. stažení přes composer
2. v presenteru kde se má zobrazit vyhledávací pole a výpis pošt - zaregistrovat do neonu a injectnout \Posta\PostManager (založen na NDB, potažmo NDAB manageru)což je implementace interface \Posta\IPostManager 
(nebo si udělat vlastní implementaci)
3. v daném presenteru si pak nechat vytvářet komponentu, vlastní zpracování výběru pošty provést v události onSelect komponenty [zavolá se po úspěšném výběru pošty]
Např. 

	protected function createComponentBalikNaPostu() {
	    $control = new \Posta\BalikControl($this->postManager);
	    $control->setValue($hodnota_dobirky);
	    $control->onSelect[] = function($posta) use ($control) {
			$control->flashMessage("Pošta ". $posta->NAZ_PROV . " vybrána", "ok");
	    };
	    return $control;
	}
4.  v šabloně (latte) daného prosenteru zadat:
{control balikNaPostu:head} - vypíše javascript a css 
{control balikNaPostu-form} - vypíše formulář pro vyhledání pošty
{control balikNaPostu} - výpis pošt po vyhledání z formuláře výše

5. import pošt do databáze:
	v presenteru si zavolám např.

	public function actionImport() {
	    try {
	      $imported  = $this->postManager->import();
	      $this->template->message = "Úspěšně naimportováno $imported záznamů o poštách";
	    } catch(\Exception $e) {
			$this->template->message = "Chyba: " . $e->getMessage();
	    }
	}