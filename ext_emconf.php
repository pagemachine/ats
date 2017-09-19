<?php

$EM_CONF['ats'] = [
	'title' => 'ATS',
	'description' => 'Extension for Job Application Management',
	'category' => 'plugin',
	'author' => 'Stefan Schütt, Rafal Kiciak, Nathalie Nabholz, Saskia Schreiber',
	'author_email' => 'sschuett@pagemachine.de, rkiciak@pagemachine.de, nnabholz@pagemachine.de, sschreiber@pagemachine.de',
	'state' => 'alpha',
	'clearCacheOnLoad' => 0,
	'version' => '0.1',
	'constraints' => [
		'depends' => [
			'typo3' => '7.6.0-8.7.99',
            'static_info_tables' => '6.3.0-6.99.99'
		]
	],
    'createDirs' => 'fileadmin/tx_ats'
];
