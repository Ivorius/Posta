<?php

/**
 * Model pro Baliky ČESKÉ POŠTY
 *
 * @author Ivo
 */

namespace Unio\Posta;

use Unio\Manager;

class PostManager extends Manager implements IPostManager
{
	const IDENTI = "CESKA_POSTA_NA_POSTU";

	/** @var string */
	protected $tableName = "posta";
	private $xmlFile = "http://napostu.cpost.cz/vystupy/napostu.xml";

	public function findByPostCode($postcode)
	{
		$psc = intval(str_replace(' ', '', $postcode));
		$result = $this->findAll()->where("psc", $psc);
		return $result;
	}

	public function findByTown($town)
	{
		$result = $this->findAll()
			->where('naz_prov LIKE ?', $town . '%');
		return $result;
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
			foreach ($xml->row AS $posta) {
				$set = array();

				foreach ($posta as $a => $b) {
					if ($a == 'OTV_DOBA') {
						foreach ($b->children() AS $x) {
							$day = $x->attributes()->name;
							$den = $this->lowerAscii($day);
							// od_do
							$count = 1;
							foreach ($x->children() as $doba) {
								// <od> a <do>
								$start = (string)$doba->od;
								$end = (string)$doba->do;

								$set[$den . "_od" . $count] = $start;
								$set[$den . "_do" . $count] = $end;
								$count++;
							}
						}
					} else
						$set[$this->lowerAscii($a)] = $this->setBool($b);
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
	 * Nastavení jakože(mysql) bool hodnot pokud je třeba
	 * @param string $value
	 * @return int|string
	 */
	private function setBool($value)
	{
		if ($value == 'A')
			return 1;
		else if ($value == 'N')
			return 0;
		else
			return (string)$value;
	}

	/**
	 * Vytvoří prázdnou tabulku a smaže tab. stejného názvu pokud existuje
	 */
	private function createTable()
	{
		$this->rawQuery("DROP TABLE IF EXISTS `" . $this->tableName . "`");
		$sql = "CREATE TABLE `" . $this->tableName . "` (
                     `id` int(5) NOT NULL AUTO_INCREMENT,
                      `psc` int(6) NOT NULL,
                      `naz_prov` varchar(100) COLLATE utf8_czech_ci NOT NULL,
                      `okres` varchar(100) COLLATE utf8_czech_ci NOT NULL,
                      `adresa` varchar(200) COLLATE utf8_czech_ci NOT NULL,
                      `v_provozu` tinyint(1) NOT NULL,
                      `prodl_doba` tinyint(1) NOT NULL,
                      `bankomat` tinyint(1) NOT NULL,
                      `parkoviste` tinyint(1) NOT NULL,
                      `komplet_servis` tinyint(1) NOT NULL,
                      `vikend` tinyint(1) NOT NULL,
                      `lokality_prodl` tinyint(1) NOT NULL,
                      `vydej_np_od` varchar(200) COLLATE utf8_czech_ci NOT NULL,
                      `ukl_np_limit` tinyint(1) NOT NULL,
                      `psc_np_nahr` varchar(100) COLLATE utf8_czech_ci NOT NULL,
                      `naz_np_nahr` varchar(100) COLLATE utf8_czech_ci NOT NULL,
                      `abc_box` tinyint(1) NOT NULL,
                      `pondeli_od1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `pondeli_do1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `pondeli_od2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `pondeli_do2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `pondeli_od3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `pondeli_do3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `utery_od1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `utery_do1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `utery_od2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `utery_do2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `utery_od3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `utery_do3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `streda_od1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `streda_do1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `streda_od2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `streda_do2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `streda_od3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `streda_do3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `ctvrtek_od1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `ctvrtek_do1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `ctvrtek_od2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `ctvrtek_do2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `ctvrtek_od3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `ctvrtek_do3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `patek_od1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `patek_do1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `patek_od2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `patek_do2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `patek_od3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `patek_do3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `sobota_od1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `sobota_do1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `sobota_od2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `sobota_do2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `sobota_od3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `sobota_do3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `nedele_od1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `nedele_do1` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `nedele_od2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `nedele_do2` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `nedele_od3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      `nedele_do3` varchar(40) COLLATE utf8_czech_ci NULL DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      FULLTEXT KEY `naz_prov` (`naz_prov`)
                    ) ENGINE=innoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;";
		$this->rawQuery($sql);
	}

	private function lowerAscii($s) {
		return \Nette\Utils\Strings::toAscii(mb_strtolower($s, 'UTF-8'));
	}

}
