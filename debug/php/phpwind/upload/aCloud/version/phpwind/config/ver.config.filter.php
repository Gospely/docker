<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Ver_Config_Filter {
	
	function filterFields($data) {
		if (! S::isArray ( $data ))
			return $data;
		$result = array ();
		foreach ( $data as $key => $fields ) {
			if (S::isArray ( $fields ))
				unset ( $fields ['password'], $fields ['safecv'] );
			$result [$key] = $fields;
		}
		return $result;
	}
}