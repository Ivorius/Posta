# Balík na poštu
Komponenta pro výběr pošty k doručení balíku.

Instalace:

1. stažení přes: composer require unio/posta
2. v presenteru kde se má zobrazit vyhledávací pole a výpis pošt - zaregistrovat do neonu a injectnout \Unio\Posta\PostManager
 a nebo \Unio\Posta\SlovakPostManager (založen na NDB, potažmo Unio\Manageru)což je implementace interface \Unio\Posta\IPostManager
(nebo si udělat vlastní implementaci)
3. v daném presenteru si pak nechat vytvářet komponentu, vlastní zpracování výběru pošty provést v události onSelect komponenty [zavolá se po úspěšném výběru pošty]
Např. 

```php
	protected function createComponentBalikNaPostu() {
	    $control = new \Unio\Posta\BalikControl($this->postManager, $this->translator);
	    $control->setValue($hodnota_dobirky);
	    $control->onSelect[] = function($posta) use ($control) {
			$control->flashMessage("Pošta ". $posta->naz_prov . " vybrána", "ok");
	    };
	    return $control;
	}

	protected function createComponentBalikNaPostuSk() {
		$control = new \Unio\Posta\BalikControl($this->slovakPostManager, $this->translator);
		$control->setLatteFile("BalikControlSlovak.latte");
		$control->onSelect[] = function($posta) use ($control) {
			$this->sesna->posta_sk = $posta->id;
			$control->flashMessage("Pošta ". $posta->nazov . " vybrána", "ok");
		};
		return $control;
	}
```

4.  v šabloně (latte) daného prosenteru zadat:

```
{control balikNaPostu:head} - vypíše javascript a css 
{control balikNaPostu-form} - vypíše formulář pro vyhledání pošty
{control balikNaPostu} - výpis pošt po vyhledání z formuláře výše
```
popřípadě slovenské pošty

```
{control balikNaPostuSk-form}
{control balikNaPostuSk}
```

5. import pošt do databáze:
	v presenteru si zavolám např.

```php
	public function actionImport() {
	    try {
	      $imported  = $this->postManager->import();
	      $this->template->message = "Úspěšně naimportováno $imported záznamů o poštách";
	    } catch(\Exception $e) {
			$this->template->message = "Chyba: " . $e->getMessage();
	    }
	}
```