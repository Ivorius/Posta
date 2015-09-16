<?php

/**
 * Model pro Baliky ČESKÉ POŠTY
 *
 * @author Ivo
 */

namespace Posta;

class PostManager extends \Ndab\Manager implements IPostManager {

    /** @var string */
    protected $tableName = "posta";
    private $xmlFile = "http://napostu.cpost.cz/vystupy/napostu.xml";

    public function findByPostCode($postcode) {
	$psc = intval(str_replace(' ', '', $postcode));
	$result = $this->findAll()->where("PSC", $psc);
	return $result;
    }

    public function findByTown($town) {
	$result = $this->findAll()
		->where('NAZ_PROV LIKE ?', $town . '%');
	return $result;
    }

    
    /**
     * Import XML to database
     * @param string $xmlFile
     * @return int
     * @throws \Exception
     */
    public function import($xmlFile = NULL) {
	$num = 0;
	
	if (is_null($xmlFile))
	    $xmlFile = $this->xmlFile;

	$xml = simplexml_load_file($this->xmlFile);
	if ($xml != FALSE) {

	    $this->beginTransaction();
	    $this->createTable();
	    foreach ($xml->children() AS $child) {
		$name = $child->getName();
		$set = array();
		if ($name == 'row') {
		    foreach ($child as $a => $b) {
			if ($a == 'OTV_DOBA') {
			    foreach ($b->children() AS $x) {
				$day = $x->attributes()->name;
				$den = mb_strtolower($day, 'UTF-8');
				$den = \Nette\Utils\Strings::toAscii($den);
				// od_do
				$count = 1;
				foreach ($x->children() as $doba) {
				    // <od> a <do>
				    $start = (string) $doba->od;
				    $end = (string) $doba->do;

				    $set[$den . "_od" . $count] = $start;
				    $set[$den . "_do" . $count] = $end;
				    $count++;
				}
			    }
			} else
			    $set[$a] = $this->setBool($b);
		    }

		    $this->table()->insert($set);
		    $num++;
		}
	    }
	    $this->commit();
	    return $num;
	} else {
	    throw new \Exception("XML files is not been loaded");
	}
    }

    /**
     * Nastavení bool hodnot pokud je třeba
     * @param <type> $value
     * @return <type> 
     */
    private function setBool($value) {
	if ($value == 'A')
	    return 1;
	else if ($value == 'N')
	    return 0;
	else
	    return (string) $value;
    }

    /**
     * Vytvoří prázdnou tabulku a smaže tab. stejného názvu pokud existuje
     */
    private function createTable() {
	$this->rawQuery("DROP TABLE IF EXISTS `" . $this->tableName . "`");
	$sql = "CREATE TABLE `" . $this->tableName . "` (
                     `ID` int(5) NOT NULL AUTO_INCREMENT,
                      `PSC` int(6) NOT NULL,
                      `NAZ_PROV` varchar(100) COLLATE utf8_czech_ci NOT NULL,
                      `OKRES` varchar(100) COLLATE utf8_czech_ci NOT NULL,
                      `ADRESA` varchar(200) COLLATE utf8_czech_ci NOT NULL,
                      `V_PROVOZU` tinyint(1) NOT NULL,
                      `PRODL_DOBA` tinyint(1) NOT NULL,
                      `BANKOMAT` tinyint(1) NOT NULL,
                      `PARKOVISTE` tinyint(1) NOT NULL,
                      `KOMPLET_SERVIS` tinyint(1) NOT NULL,
                      `VIKEND` tinyint(1) NOT NULL,
                      `LOKALITY_PRODL` tinyint(1) NOT NULL,
                      `VYDEJ_NP_OD` varchar(200) COLLATE utf8_czech_ci NOT NULL,
                      `UKL_NP_LIMIT` tinyint(1) NOT NULL,
                      `PSC_NP_NAHR` varchar(100) COLLATE utf8_czech_ci NOT NULL,
                      `NAZ_NP_NAHR` varchar(100) COLLATE utf8_czech_ci NOT NULL,
                      `ABC_BOX` tinyint(1) NOT NULL,
                      `pondeli_od1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `pondeli_do1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `pondeli_od2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `pondeli_do2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `pondeli_od3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `pondeli_do3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `utery_od1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `utery_do1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `utery_od2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `utery_do2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `utery_od3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `utery_do3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `streda_od1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `streda_do1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `streda_od2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `streda_do2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `streda_od3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `streda_do3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `ctvrtek_od1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `ctvrtek_do1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `ctvrtek_od2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `ctvrtek_do2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `ctvrtek_od3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `ctvrtek_do3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `patek_od1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `patek_do1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `patek_od2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `patek_do2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `patek_od3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `patek_do3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `sobota_od1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `sobota_do1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `sobota_od2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `sobota_do2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `sobota_od3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `sobota_do3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `nedele_od1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `nedele_do1` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `nedele_od2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `nedele_do2` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `nedele_od3` varchar(40) COLLATE utf8_czech_ci NOT NULL,
                      `nedele_do3` varchar(40) COLLATE utf8_czech_ci NOT NULL,                      
                      PRIMARY KEY (`ID`),
                      FULLTEXT KEY `NAZ_PROV` (`NAZ_PROV`)
                    ) ENGINE=innoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;";
	$this->rawQuery($sql);
    }

}
