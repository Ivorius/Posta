<?php
/**
 *
 * @author Ivo
 */
namespace Posta;

interface IPostManager {
    
    public function findByPostCode($postcode);
    
    public function findByTown($town);
    
    /**
     * Import XML to database
     * @param string $xmlFile
     * @return int
     * @throws \Exception
     */
    public function import($xmlFile);
}
