# Balík na poštu
Komponenta pro výběr pošty k doručení balíku, balíkovny, balikoboxu (sk)

Instalace:

1. stažení přes: composer require unio/posta
2. v neonu si zaregistrovat potřebné Unio\Posta\IRepository (např. BalikovnaRepository)
3. zaregistrovat si v neonu konkrétní továrníčku IXxxControlFactory na komponenty (např. IBalikovnaControlFactory)
4. vytvořit si komponentu a pořešit vlastní zpracování v události onSelect komponenty [zavolá se po úspěšném výběru pošty]
Např. 

```php

	protected function createComponentBalikovna(): BalikovnaControl
	{
		$control = $this->balikovnaControlFactory->create();
		$control->onSelect[] = function(IShipBox $balikovna) {
			$this->sesna->shipbox[BalikovnaRepository::IDENTITY] = $balikovna->getId();
		};
		return $control;
	}
```

5. v šabloně (latte) daného prosenteru zadat:

```
{control balikovna:head} - vypíše javascript a css 
{control balikovna-form} - vypíše formulář pro vyhledání balikovny
{control balikovna} - výpis balikoven po vyhledání z formuláře výše
```
popřípadě slovenské pošty

```
{control balikNaPostuSk-form}
{control balikNaPostuSk}
```

6. import pošt do databáze:
	v presenteru si zavolám např.

```php
	public function actionImport() {
	    try {
	      $this->balikovnaRepository->import();
	    } catch(\Exception $e) {
			$this->template->message = "Chyba: " . $e->getMessage();
	    }
	}
```
