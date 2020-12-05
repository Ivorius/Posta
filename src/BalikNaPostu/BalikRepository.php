<?php


namespace Unio\Posta\BalikNaPostu;

use GuzzleHttp\Client;
use Nette\Utils\Strings;
use Unio\Posta\IPostManager;
use Unio\Posta\IRepository;
use Unio\Shipping\IShipBox;

class BalikRepository implements IRepository
{
	public const IDENTITY = "CESKA_POSTA_NA_POSTU";
	public const naPostuXmlExt = "http://napostu.cpost.cz/vystupy/napostu.xml";
	public static $naPostuXml = WWW_DIR . '/soubory/posta/napostu.xml';


	public function getById($id): \SimpleXMLElement
	{
		$xml = $this->getXml();
		$array = $xml->xpath('//p:row[p:PSC="' . $id . '"]');
		if ($array === false) {
			throw new \Exception('Balikovna has not this PSC' . $id);
		}
		if (count($array) > 1) {
			throw new \Exception('Balikovna has more than one PSC' . $id);
		}

		return current($array);
	}

	public function getBoxById($id): BalikBoxDTO
	{
		return new BalikBoxDTO($this->getById($id));
	}

	public function getXml(): \SimpleXMLElement
	{
		$xml = simplexml_load_file(self::$naPostuXml);
		$xml->registerXPathNamespace('p', 'http://www.cpost.cz/schema/aict/zv');
		return $xml;
	}

	public function search(string $query): array
	{
		$xml = $this->getXml();
		$entries = $finals = [];

		$entries += $xml->xpath('//p:row[p:PSC="' . $query . '"]');
		$entries += $xml->xpath('//p:row[contains(p:NAZ_PROV, "' . Strings::firstUpper($query) . '")]');
		$entries += $xml->xpath('//p:row[contains(p:ADRESA, "' . Strings::firstUpper($query) . '")]');

		foreach ($entries as $entry) {
			$finals[(string)$entry->PSC] = $entry;
		}

		return $finals;
	}

	public function import()
	{
		try {
			$client = new Client();
			$file = $client->request('GET', self::naPostuXmlExt, ['sink' => self::$naPostuXml]);
		} catch (GuzzleException $e) {
			throw new \Exception(self::balikovnaXmlExt . 'is missing. Balik na poštu can not be updated', $e);
		}
	}

}
