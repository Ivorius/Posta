<?php declare(strict_types=1);
/**
 * Author: Ivo Toman
 */

namespace Unio\Posta\BalikNaPostu;


interface IBalikControlFactory
{
	public function create(): BalikControl;
}
