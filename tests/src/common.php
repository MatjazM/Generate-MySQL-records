<?php

/**
 * Common class and variables for DataGeneratorTest.php and MemoryUsageTest.php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

/**
 * Used in DataGeneratorTest.php and MemoryUsageTest.php
 * @var array $columns
 */
$columns = array(
	'testTable1' => array(
		'id' => array(
			'0' => 'bigint(20) unsigned',
			'1' => 'auto_increment'
		),
		'name' => array(
			'0' => 'varchar(200)',
			'1' => ''
		)
	),
	'testTable2' => array(
		'id' => array(
			'0' => 'bigint(20)',
			'1' => ''
		),
		'name' => array(
			'0' => 'varchar(200)',
			'1' => ''
		)
	),
	'testTable3' => array(
		'id' => array(
			'0' => 'tinyint',
			'1' => ''
		)
	),
	'testTable4' => array(
		'id1' => array(
			'0' => 'tinyint',
			'1' => ''
		),
		'id2' => array(
			'0' => 'smallint',
			'1' => '',
			'2' => 10
		),
		'id3' => array(
			'0' => 'mediumint',
			'1' => '',
		),
		'id4' => array(
			'0' => 'int',
			'1' => ''
		),
		'id5' => array(
			'0' => 'bigint',
			'1' => ''
		),
		'f1' => array(
			'0' => 'float',
			'1' => ''
		),
		'f2' => array(
			'0' => 'double',
			'1' => ''
		),
		'f3' => array(
			'0' => 'real',
			'1' => ''
		),
		'f4' => array(
			'0' => 'decimal(10,2)',
			'1' => ''
		),
		's1' => array(
			'0' => 'varchar(255)',
			'1' => ''
		),
		's2' => array(
			'0' => 'char',
			'1' => ''
		),
		's3' => array(
			'0' => 'text',
			'1' => ''
		),
		's4' => array(
			'0' => 'tinytext',
			'1' => ''
		),
		's5' => array(
			'0' => 'mediumtext',
			'1' => ''
		),
		's6' => array(
			'0' => 'longtext',
			'1' => ''
		),
		's7' => array(
			'0' => 'tinyblob',
			'1' => ''
		),
		's8' => array(
			'0' => 'blob',
			'1' => ''
		),
		's9' => array(
			'0' => 'mediumblob',
			'1' => ''
		),
		's10' => array(
			'0' => 'longblob',
			'1' => ''
		),
		'd1' => array(
			'0' => 'date',
			'1' => ''
		),
		'd2' => array(
			'0' => 'datetime',
			'1' => ''
		),
		'd3' => array(
			'0' => 'time',
			'1' => ''
		),
		'd4' => array(
			'0' => 'year',
			'1' => ''
		),
		'o1' => array(
			'0' => 'enum(\'1\', \'2\', \'3\')',
			'1' => ''
		),
		'o2' => array(
			'0' => 'set(\'1\', \'2\', \'3\')',
			'1' => ''
		),
		'o3' => array(
			'0' => 'bit(5)',
			'1' => ''
		)
	)
);

/**
 * Used in DataGeneratorTest.php and MemoryUsageTest.php
 * @var array $indexes
 */
$indexes = array(
	'testTable3' => array(
		'1' => array(
			'unique' => true,
			'indexes' => array(
				'0' => 'id'
			)
		)
	)
);

/**
 * Used in DataGeneratorTest.php and MemoryUsageTest.php
 * Dummy config, so we don't have dependincies
 */
class ConfigUnitTest implements ConfigInterface {

	/**
	 * Constructor that sets config that DataGenerator and MemoryUsage class needs
	 */
	public function __construct() {
		$this->{'DATAGENERATOR_TINYINT_MAX_SIZE'} = 127;
		$this->{'DATAGENERATOR_SMALLINT_MAX_SIZE'} = 32767;
		$this->{'DATAGENERATOR_MEDIUMINT_MAX_SIZE'} = 8388607;
		$this->{'DATAGENERATOR_INT_MAX_SIZE'} = 2147483647;
		$this->{'DATAGENERATOR_BIGINT_MAX_SIZE'} = 9223372036854775807;
		$this->{'DATAGENERATOR_FLOAT_MAX_SIZE'} = 3.402823466E+38;
		$this->{'DATAGENERATOR_DOUBLE_MAX_SIZE'} = 1.7976931348623157E+308;
		$this->{'DATAGENERATOR_TINYTEXT_MAX_SIZE'} = 255;
		$this->{'DATAGENERATOR_TEXT_MAX_SIZE'} = 65535;
		$this->{'DATAGENERATOR_MEDIUMTEXT_MAX_SIZE'} = 16777215;
		$this->{'DATAGENERATOR_LONGTEXT_MAX_SIZE'} = 4294967295;

		$this->{'DATAGENERATOR_VARCHAR_RATIO'} = 10;
		$this->{'DATAGENERATOR_TINYTEXT_RATIO'} = 10;
		$this->{'DATAGENERATOR_TEXT_RATIO'} = 1000;
		$this->{'DATAGENERATOR_MEDIUMTEXT_RATIO'} = 1000000;
		$this->{'DATAGENERATOR_LONGTEXT_RATIO'} = 1000000000;
	}
}