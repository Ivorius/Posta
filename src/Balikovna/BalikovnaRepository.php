<?php
declare(strict_types=1);

namespace Unio\Posta\Balikovna;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nette\Utils\Strings;
use Unio\Posta\IRepository;
use Unio\Shipping\IShipBox;

class BalikovnaRepository implements IRepository
{
	public const IDENTITY = "CESKA_POSTA_BALIKOVNA";
	public const balikovnaXmlExt = 'http://napostu.ceskaposta.cz/vystupy/balikovny.xml';
	public static $balikovnaXml = WWW_DIR . '/soubory/posta/balikovna.xml';


	public function getById($id): \SimpleXMLElement
	{
		$xml = $this->getXml();
		$array = $xml->xpath('//p:row[p:PSC="' . $id . '"]');
		if($array === false)  {
			throw new \Exception('Balikovna has not this PSC' . $id);
		}
		if(count($array) > 1) {
			throw new \Exception('Balikovna has more than one PSC' . $id);
		}

		return current($array);
	}

	public function getBoxById($id): BalikovnaBoxDTO
	{
		return new BalikovnaBoxDTO($this->getById($id));
	}

	public function search(string $query): array
	{
		$xml =  $this->getXml();
		$entries = $finals = [];

		$entries += $xml->xpath('//p:row[p:PSC="' . $query . '"]');
		$entries += $xml->xpath('//p:row[contains(p:NAZEV, "' . Strings::firstUpper($query) . '")]');
		$entries += $xml->xpath('//p:row[contains(p:ADRESA, "' . Strings::firstUpper($query) . '")]');

		foreach($entries AS $entry) {
			$finals[(string) $entry->PSC] = $entry;
		}

		return $finals;
	}

	public function getXml(): \SimpleXMLElement
	{
		$xml = simplexml_load_file( self::$balikovnaXml);
		$xml->registerXPathNamespace('p', 'http://www.cpost.cz/schema/aict/zv_2');
		return $xml;
	}

	public function import()
	{
		try {
			$client = new Client();
			$file = $client->request('GET', self::balikovnaXmlExt, ['sink' => self::$balikovnaXml]);
		} catch (GuzzleException $e) {
			throw new \Exception(self::balikovnaXmlExt . 'is missing. Balikovna can not be updated', $e);
		}
	}

}
