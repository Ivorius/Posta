<?php declare(strict_types=1);
/**
 * Author: Ivo Toman
 */

namespace Unio\Posta;


use Unio\Shipping\IShipBox;

interface IRepository
{
	public function getById($id);
//	PHP >= 7.4
//	public function getBoxById($id): IShipBox;
	public function getBoxById($id);
	public function search(string $query);
	public function import();
}
