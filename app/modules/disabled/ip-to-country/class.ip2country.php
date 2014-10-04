<?php
namespace Module\IP2Country;

require_once(DIRBASE . '/modules/enabled/file-system-database/class.fsdb.php');

/**
 * Cross-references countries with IP addresses using a flat-file-database.
 *
 * ### Usage
 *
 * <code>
 *   $i = new \Module\IP2Country\Converter();
 *   $countryCode = $i->getCountryCode($_SERVER['REMOTE_ADDR']);
 *
 *   if ($countryCode != false) {
 *     echo $countryCode;
 *   } else {
 *     echo $_SERVER['REMOTE_ADDR'] .
 *       "(" . ip2long($_SERVER['REMOTE_ADDR']) .
 *       ") Not found in " . $i->getTable($_SERVER['REMOTE_ADDR']) . ".";
 *   }
 * </code>
 *
 * ### Changelog
 *
 * ## Version 1.2
 * * Added namespacing
 *
 * ## Version 1.1
 * * Added the date section to documentation
 *
 * ### Dependencies
 * * class.fsdb.php
 *
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.2
 * @license  http://opensource.org/licenses/MIT
 */
class Converter {
	private $db;

	private $upperRange = array(
		50000000,    100000000,  150000000,  200000000,  250000000,  300000000,
		350000000,   400000000,  450000000,  500000000,  550000000,  600000000,
		650000000,   700000000,  750000000,  800000000,  850000000,  900000000,
		950000000,  1000000000, 1050000000, 1100000000, 1150000000, 1200000000,
		1250000000, 1300000000, 1350000000, 1400000000, 1450000000, 1500000000,
		1550000000, 1600000000, 1650000000, 1700000000, 1750000000, 1800000000,
		1850000000, 1900000000, 1950000000, 2000000000, 2050000000, 2100000000,
		2150000000, 2200000000, 2250000000, 2300000000, 2350000000, 2400000000,
		2450000000, 2500000000, 2550000000, 2600000000, 2650000000, 2700000000,
		2750000000, 2800000000, 2850000000, 2900000000, 2950000000, 3000000000,
		3050000000, 3100000000, 3150000000, 3200000000, 3250000000, 3300000000,
		3350000000, 3400000000, 3450000000, 3500000000, 3550000000, 3600000000,
		3650000000, 3700000000, 3750000000, 3800000000, 3850000000, 3900000000,
		3950000000, 4000000000
	);

	public function __construct() {
		$this->db = new \FSDB\Connection('./ipData/');
	}

	public function getCountryCode($ip) {
		$dec = ip2long($ip);
		$data = $this->db->select($this->getTable($ip), "*");
		foreach ($data as $row) {
			if ($row->startIP <= $dec && $row->endIP >= $dec) {
				return $row->countryCode;
			}
		}

		return false;
	}

	public function getTable($ip) {
		$dec = ip2long($ip);

		foreach ($this->upperRange as $limit) {
			if ($dec >= $limit - 50000000 && $dec <= $limit) {
				return (string) $limit;
			}
		}

		return false;
	}
}