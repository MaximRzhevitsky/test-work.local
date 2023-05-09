<?php

class WLD_QM_Base {
	public static function init() : void {
		add_filter(
			'qm/output/file_link_format',
			array( static::class, 'file_link_format' )
		);
	}

	public static function file_link_format() : string {
		// https://tracy.nette.org/en/open-files-in-ide
		return 'editor://open/?file=%f&line=%l';
	}
}
