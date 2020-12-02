<?php declare(strict_types=1);
/**
 * Author: Ivo Toman
 */

namespace Unio\Posta\Balikovna;


interface IBalikovnaControlFactory
{

	public function create(): BalikovnaControl;

}
