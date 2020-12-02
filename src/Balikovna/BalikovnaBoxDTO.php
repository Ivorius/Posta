<?php declare(strict_types=1);
/**
 * Author: Ivo Toman
 */

namespace Unio\Posta\Balikovna;


use Unio\Shipping\IShipBox;

class BalikovnaBoxDTO implements IShipBox
{

	/**
	 * @var \SimpleXMLElement
	 */
	private $box;

	public function __construct(\SimpleXMLElement $box)
	{
		$this->box = $box;
	}

	public function getType():string
	{
		return 'Balíkovna';
	}

	public function getId()
	{
		return (string) $this->box->PSC;
	}

	public function getName(): string
	{
		return (string) $this->box->NAZEV;
	}

	public function getAddress(): string
	{
		return (string) $this->box->ADRESA;
	}

	public function getCity(): string
	{
		$adresa =  explode(',',  (string) $this->box->ADRESA);
		return (string) count($adresa) === 4 ? $adresa[3] . ' - ' . $adresa[1] : end($adresa);
	}

	public function getStreet(): string
	{
		$adresa =  explode(',',  (string) $this->box->ADRESA);
		return (string) current($adresa);
	}

	public function getZip(): string
	{
		return $this->getId();
	}
}
