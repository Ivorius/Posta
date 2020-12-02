<?php declare(strict_types=1);
/**
 * Author: Ivo Toman
 */

namespace Unio\Posta\SlovakBalikobox;


interface IBalikoboxControlFactory
{
	public function create(): BalikoboxControl;
}

