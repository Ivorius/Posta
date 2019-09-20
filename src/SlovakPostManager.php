<?php

/**
 * Model pro Baliky SLOVENSKÉ POŠTY
 *
 * @author Ivo
 */

namespace Unio\Posta;

use Unio\Manager;

class SlovakPostManager extends Manager implements IPostManager
{
	const IDENTI = "SLOVENSKA_POSTA_NA_POSTU";

	/** @var string */
	protected $tableName = "posta_sk";
	private $xmlFile = "https://www.posta.sk/public/forms/zoznam_post.xml";

	public function findByPostCode($postcode)
	{
		$psc = intval(str_replace(' ', '', $postcode));
		return $this->table()->where("psc", $psc)->where("active", 1);
	}

	public function findByTown($town)
	{
		return $this->table()->where('obec LIKE ?', $town . '%')->where("active", 1);
	}


	/**
	 * Import XML to database
	 * @param string $xmlFile
	 * @return int
	 * @throws \Exception
	 */
	public function import($xmlFile = NULL)
	{
		$num = 0;

		if (is_null($xmlFile))
			$xmlFile = $this->xmlFile;

		$xml = simplexml_load_file($xmlFile);
		if ($xml != FALSE) {

			$this->beginTransaction();
			$this->rawQuery("SET foreign_key_checks = 0");
			$this->createTable();

			foreach ($xml->POSTA AS $posta) {
				$set = array(
					"id" => (int)$posta->ID,
					"nazov" => $posta->NAZOV,
					"psc" => $posta->PSC,
					"telefon" => $posta->TELEFON,
					"ulica" => $posta->ADRESA->ULICA,
					"cislo" => $posta->ADRESA->CISLO,
					"obec" => $posta->ADRESA->OBEC,
					'podaj_do' => ($posta->HMOTNOSTNE_LIMITY->PRODUKT->PODAJ_DO),
					'dodaj_do' => ($posta->HMOTNOSTNE_LIMITY->PRODUKT->DODAJ_DO),
					'gps_lon' => ($posta->GPS->LONGITUDE),
					'gps_lat' => ($posta->GPS->LATITUDE)
				);

				foreach ($posta->HODINY_PRE_VEREJNOST->children() as $day => $x) {
					$den = mb_substr(mb_strtolower($day, 'UTF-8'), 0, 3);
					$den = \Nette\Utils\Strings::toAscii($den);
					foreach ($x->children() as $key => $cas) {
						$set[$den . "_" . mb_strtolower($key, 'UTF-8')] = $cas;
					}
				}

				$this->table()->insert($set);
				$num++;
			}

			$this->rawQuery("SET foreign_key_checks = 1");
			$this->commit();
			return $num;
		} else {
			throw new \Exception("XML files is not been loaded");
		}
	}


	/**
	 * Vytvoří prázdnou tabulku a smaže tab. stejného názvu pokud existuje
	 */
	private	function createTable()
	{
		$this->rawQuery("DROP TABLE IF EXISTS `" . $this->tableName . "`");
		$sql = "CREATE TABLE `" . $this->tableName . "` (
                                                    `id` int(6) NOT NULL,
                                                    `nazov` varchar(255) NOT NULL,
                                                    `psc` varchar(8) NOT NULL,
                                                    `telefon` varchar(20),
                                                    `ulica` varchar(255) NOT NULL,
                                                    `cislo` int(6) NOT NULL,
                                                    `obec` varchar(100) NOT NULL,
                                                    `pon_od` varchar(5),
                                                    `pon_do` varchar(5),
                                                    `pon_prestavka1_od` varchar(5),
                                                    `pon_prestavka1_do` varchar(5),
                                                    `pon_prestavka2_od` varchar(5),
                                                    `pon_prestavka2_do` varchar(5),
                                                    `uto_od` varchar(5),
                                                    `uto_do` varchar(5),
                                                    `uto_prestavka1_od` varchar(5),
                                                    `uto_prestavka1_do` varchar(5),
                                                    `uto_prestavka2_od` varchar(5),
                                                    `uto_prestavka2_do` varchar(5),
                                                    `str_od` varchar(5),
                                                    `str_do` varchar(5),
                                                    `str_prestavka1_od` varchar(5),
                                                    `str_prestavka1_do` varchar(5),
                                                    `str_prestavka2_od` varchar(5),
                                                    `str_prestavka2_do` varchar(5),
                                                    `stv_od` varchar(5),
                                                    `stv_do` varchar(5),
                                                    `stv_prestavka1_od` varchar(5),
                                                    `stv_prestavka1_do` varchar(5),
                                                    `stv_prestavka2_od` varchar(5),
                                                    `stv_prestavka2_do` varchar(5),
                                                    `pia_od` varchar(5),
                                                    `pia_do` varchar(5),
                                                    `pia_prestavka1_od` varchar(5),
                                                    `pia_prestavka1_do` varchar(5),
                                                    `pia_prestavka2_od` varchar(5),
                                                    `pia_prestavka2_do` varchar(5),
                                                    `sob_od` varchar(5),
                                                    `sob_do` varchar(5),
                                                    `sob_prestavka1_od` varchar(5),
                                                    `sob_prestavka1_do` varchar(5),
                                                    `sob_prestavka2_od` varchar(5),
                                                    `sob_prestavka2_do` varchar(5),
                                                    `ned_od` varchar(5),
                                                    `ned_do` varchar(5),
                                                    `ned_prestavka1_od` varchar(5),
                                                    `ned_prestavka1_do` varchar(5),
                                                    `ned_prestavka2_od` varchar(5),
                                                    `ned_prestavka2_do` varchar(5),
                                                    `podaj_do` int(6),
                                                    `dodaj_do` int(6),
                                                    `gps_lon` varchar(20) NOT NULL,
                                                    `gps_lat` varchar(20) NOT NULL,
                                                    `active` TINYINT(1) NOT NULL DEFAULT '1',
                                                    PRIMARY KEY(`id`)
                                             ) ENGINE=innoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
		$this->rawQuery($sql);
	}

}
