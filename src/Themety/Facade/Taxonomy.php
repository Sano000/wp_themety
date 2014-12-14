<?php
namespace Themety\Facade;

use Illuminate\Support\Facades\Facade;

class Taxonomy extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'content.taxonomy'; }

}

