<?php

// +----------------------------------------------------------------------+
// | Copyright 2013  Madpixels  (email : visualizer@madpixels.net)        |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+
// | Author: Eugene Manuilov <eugene@manuilov.org>                        |
// +----------------------------------------------------------------------+

/**
 * The abstract class for source managers.
 *
 * @category Visualizer
 * @package Source
 *
 * @since 1.0.0
 * @abstract
 */
abstract class Visualizer_Source {

	/**
	 * The array of data.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @var array
	 */
	protected $_data = array();

	/**
	 * The array of series.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @var array
	 */
	protected $_series = array();

	/**
	 * Returns source name.
	 *
	 * @since 1.0.0
	 *
	 * @abstract
	 * @access public
	 * @return string The name of source.
	 */
	public abstract function getSourceName();

	/**
	 * Fetches information from source, parses it and builds series and data arrays.
	 *
	 * @since 1.0.0
	 *
	 * @abstract
	 * @access public
	 */
	public abstract function fetch();

	/**
	 * Returns series parsed from source.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return array The array of series.
	 */
	public function getSeries() {
		return json_encode( $this->_series );
	}

	/**
	 * Returns data parsed from source.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return array The array of data.
	 */
	public function getData() {
		return json_encode( $this->_data );
	}

	/**
	 * Normalizes values according to series' type.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @param array $data The row of data.
	 * @return array Normalized row of data.
	 */
	protected function _normalizeData( $data ) {
		// normalize values
		foreach ( $this->_series as $i => $series ) {
			// if no value exists for the seires, then add null
			if ( !isset( $data[$i] ) ) {
				$data[$i] = null;
			}

			if ( is_null( $data[$i] ) ) {
				continue;
			}

			switch ( $series['type'] ) {
				case 'number':
					$data[$i] = is_float( $data[$i] )
						? floatval( $data[$i] )
						: intval( $data[$i] );
					break;
				case 'boolean':
					$data[$i] = filter_validate( $data[$i], FILTER_VALIDATE_BOOLEAN );
					break;
				case 'timeofday':
					$date = new DateTime( '1984-03-16T' . $data[$i] );
					if ( $date ) {
						$data[$i] = array(
							intval( $date->format( 'H' ) ),
							intval( $date->format( 'i' ) ),
							intval( $date->format( 's' ) ),
							0,
						);
					}
					break;
			}
		}

		return $data;
	}

}