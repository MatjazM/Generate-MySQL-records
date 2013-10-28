<?php

/**
 * Settings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

$config = array();

/**
 * Settings for MySQL
 */
$config['MYSQL_HOSTNAME'] = '127.0.0.1';
$config['MYSQL_USERNAME'] = 'root';
$config['MYSQL_PASSWORD'] = '';
$config['MYSQL_SCHEMA'] = 'test';

/**
 * Default values in MySQL
 */
$config['DATAGENERATOR_TINYINT_MAX_SIZE'] = 127;
$config['DATAGENERATOR_SMALLINT_MAX_SIZE'] = 32767;
$config['DATAGENERATOR_MEDIUMINT_MAX_SIZE'] = 8388607;
$config['DATAGENERATOR_INT_MAX_SIZE'] = 2147483647;
$config['DATAGENERATOR_BIGINT_MAX_SIZE'] = 9223372036854775807;
$config['DATAGENERATOR_FLOAT_MAX_SIZE'] = 3.402823466E+38;
$config['DATAGENERATOR_DOUBLE_MAX_SIZE'] = 1.7976931348623157E+308;
$config['DATAGENERATOR_TINYTEXT_MAX_SIZE'] = 255;
$config['DATAGENERATOR_TEXT_MAX_SIZE'] = 65535;
$config['DATAGENERATOR_MEDIUMTEXT_MAX_SIZE'] = 16777215;
$config['DATAGENERATOR_LONGTEXT_MAX_SIZE'] = 4294967295;

/**
 * Because texts can be very large, you may need to reduce it
 * Scripts generate text length between (1 - (DATAGENERATOR_*_MAX_SIZE / DATAGENERATOR_*_RATIO)
 */
$config['DATAGENERATOR_VARCHAR_RATIO'] = 1;
$config['DATAGENERATOR_TINYTEXT_RATIO'] = 10;
$config['DATAGENERATOR_TEXT_RATIO'] = 1000;
$config['DATAGENERATOR_MEDIUMTEXT_RATIO'] = 1000000;
$config['DATAGENERATOR_LONGTEXT_RATIO'] = 1000000000;