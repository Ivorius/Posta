<?php

namespace Unio\Posta\SlovakBalikobox;

use GuzzleHttp\Client;
use Nette\Utils\Strings;
use Unio\Posta\IRepository;
use Unio\Shipping\IShipBox;

class BalikoboxRepository implements IRepository
{
	const IDENTITY = "SLOVENSKA_POSTA_NA_POSTU";
	public const naPostuXmlExt = "https://www.posta.sk/public/forms/zoznam_post.xml";
	public static $naPostuXml = WWW_DIR . '/soubory/posta/sk_zoznam_post.xml';

	public function getById($id): \SimpleXMLElement
	{
		$xml = $this->getXml();
		$array = $xml->xpath('//p:POSTA[p:ID="' . $id . '"]');
		return current($array);
	}

	public function getBoxById($id): BalikoboxDTO
	{
		return new BalikoboxDTO($this->getById($id));
	}

	public function getXml(): \SimpleXMLElement
	{
		$xml = simplexml_load_file(self::$naPostuXml);
		$xml->registerXPathNamespace('p', 'http://www.posta.sk/public/forms');
		return $xml;
	}

	public function search(string $query): array
	{
		$xml = $this->getXml();
		$entries = $finals = [];

		$entries += $xml->xpath('//p:POSTA[p:PSC="' . $query . '"]');
		$entries += $xml->xpath('//p:POSTA[contains(p:NAZOV, "' . Strings::firstUpper($query) . '")]');
		$entries += $xml->xpath('//p:POSTA/p:ADRESA[contains(p:OBEC, "' . Strings::firstUpper($query) . '")]');

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
			throw new \Exception(self::balikovnaXmlExt . 'is missing. Balikobox can not be updated', $e);
		}
	}

}
