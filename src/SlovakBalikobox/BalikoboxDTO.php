<?php declare(strict_types=1);
/**
 * Author: Ivo Toman
 */

namespace Unio\Posta\SlovakBalikobox;


use Unio\Shipping\IShipBox;

class BalikoboxDTO implements IShipBox
{
	/**
	 * @var \SimpleXMLElement
	 */
	private $box;

	public function __construct(\SimpleXMLElement $box)
	{
		$this->box = $box;
	}

	public function getType(): string
	{
		return 'Balík na poštu / BalíkoBOX';
	}

	public function getName(): string
	{
		return (string)$this->box->NAZOV;
	}

	public function getAddress(): string
	{
		return $this->getStreet() . ', ' . $this->getZip() . ' ' . $this->getCity();
	}

	public function getCity(): string
	{
		return (string) $this->box->ADRESA->OBEC;
	}

	public function getStreet(): string
	{
		return (string) $this->box->ADRESA->ULICA . ' ' . $this->box->ADRESA->CISLO;
	}

	public function getZip(): string
	{
		return (string) $this->box->PSC;
	}

	public function getId()
	{
		return (string)$this->box->ID;
	}
}
