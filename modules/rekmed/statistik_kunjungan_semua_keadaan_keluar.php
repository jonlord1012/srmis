<?
$_TITLE = "Statistik Kunjungan Berdasar Keadaan Keluar";
Class Statistik_Kunjungan_Semua_Keadaan_Keluar {
	function get_pasien_check($val) {
		$val[tgl_periksa_tgl_start] = empty($val[tgl_periksa_tgl_start])?1:$val[tgl_periksa_tgl_start];
		$val[tgl_periksa_bln_start] = empty($val[tgl_periksa_bln_start])?1:$val[tgl_periksa_bln_start];
		$val[tgl_periksa_tgl_end] = empty($val[tgl_periksa_tgl_end])?1:$val[tgl_periksa_tgl_end];
		$val[tgl_periksa_bln_end] = empty($val[tgl_periksa_bln_end])?1:$val[tgl_periksa_bln_end];

		$objResponse = new xajaxResponse;

		$tgl_start = strtotime($val[tgl_periksa_thn_start] . "-" . $val[tgl_periksa_bln_start] . "-" . $val[tgl_periksa_tgl_start]);
		$tgl_end = strtotime($val[tgl_periksa_thn_end] . "-" . $val[tgl_periksa_bln_end] . "-" . $val[tgl_periksa_tgl_end]);

		if(!checkdate($val[tgl_periksa_bln_start], $val[tgl_periksa_tgl_start], $val[tgl_periksa_thn_start])) {
			$objResponse->addAlert("Tanggal Awal Tidak Valid");
			$objResponse->addScriptCall("fokus", "tgl_periksa_tgl_start");
		} elseif(!checkdate($val[tgl_periksa_bln_end], $val[tgl_periksa_tgl_end], $val[tgl_periksa_thn_end])) {
			$objResponse->addAlert("Tanggal Akhir Tidak Valid");
			$objResponse->addScriptCall("fokus", "tgl_periksa_tgl_start");
		} elseif($tgl_start > $tgl_end) {
			$objResponse->addAlert("Tanggal Awal Harus Kurang Dari Tanggal Akhir");
			$objResponse->addScriptCall("fokus", "tgl_periksa_tgl_start");
		} else {
			$objResponse->addScriptCall("xajax_get_pasien", $val);
		}
		return $objResponse;
	}

	function get_pasien($val) {
		$tgl_start = @date("Y-m-d", @mktime(1,1,1,$val[tgl_periksa_bln_start], $val[tgl_periksa_tgl_start], $val[tgl_periksa_thn_start]));
		$tgl_end = @date("Y-m-d", @mktime(1,1,1,$val[tgl_periksa_bln_end], $val[tgl_periksa_tgl_end], $val[tgl_periksa_thn_end]));
		
		$title = "Statistik Kunjungan Berdasar Keadaan Keluar";
		
		unset($_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar]);

		$kon = new Konek;
		if($val[jangka_waktu] == "hari") {
			$tanggal_awal = tanggalIndo($tgl_start, "j F Y");
			$tanggal_akhir = tanggalIndo($tgl_end, "j F Y");
			$_q .= "AND DATE(kk.tgl_daftar) BETWEEN '" . $tgl_start . "' AND '" . $tgl_end . "'";
		} elseif($val[jangka_waktu] == "bulan") {
			$tanggal_awal = tanggalIndo($tgl_start, "F Y");
			$tanggal_akhir = tanggalIndo($tgl_end, "F Y");
			$_q .= "AND EXTRACT(YEAR_MONTH FROM kk.tgl_daftar) BETWEEN EXTRACT(YEAR_MONTH FROM '" . $tgl_start . "') AND EXTRACT(YEAR_MONTH FROM '" . $tgl_end . "')";
		} else {
			$tanggal_awal = tanggalIndo($tgl_start, "Y");
			$tanggal_akhir = tanggalIndo($tgl_end, "Y");
			$_q .= "AND YEAR(kk.tgl_daftar) BETWEEN YEAR('" . $tgl_start . "') AND YEAR ('" . $tgl_end . "')";
		}

		$title .= "\nPeriode " . $tanggal_awal . " s.d. " . $tanggal_akhir;
		$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][title] = $title;
		$sql = "
			SELECT
				k.keadaan_keluar as nama,
				COUNT(k.id) as jml
			FROM
				kunjungan k
				JOIN kunjungan_kamar kk ON (kk.kunjungan_id = k.id)
			WHERE
				keadaan_keluar IS NOT NULL
				$_q
			GROUP BY
				1
			ORDER BY 1
		";
		$kon->sql = $sql;
		$kon->execute();
		$data = $kon->getAll();
		$objResponse = new xajaxResponse();
		//$objResponse->addAssign("debug", "innerHTML", $sql);
		$table = new Table;
		$table->scroll = false;
		$table->addTh("No", "Keadaan Keluar", "Jumlah", "%");
		$table->addExtraTh(
			"style=\"width:50px\"",
			"style=\"width:200px\"",
			"",
			"style=\"width:70px\""
		);
		$table->addTh("1", "2", "3", "4");
		for($i=0;$i<sizeof($data);$i++) {
			$total = $total+$data[$i][jml];
		}
		for($i=0;$i<sizeof($data);$i++) {
			$persen = round($data[$i][jml]/$total * 100, 2);
			$table->addRow(($i+1), $data[$i][nama], $data[$i][jml], $persen);

			$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][no][$i] = ($i+1);
			$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][nama][$i] = $data[$i][nama];
			$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][jml][$i] = $data[$i][jml];
			$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][persen][$i] = $persen;
			$persen_total += $persen;
		}
		$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][total] = $total;
		$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][persen_total] = round($persen_total);
		$table->addRow("", "<b>Total</b>", $_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][total], $_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][persen_total]);
		$ret = $table->build();
		if(empty($_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][jml])) $_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][jml][0] = 1;
		if(empty($_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][nama]))	$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][nama][0] = "No Data";
		

		$objResponse->addAssign("list_data", "innerHTML", $ret);
		$objResponse->addAssign("title", "innerHTML", nl2br($_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][title]));
		$_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][graph] = "<img src=\"" . URL . "rekmed/statistik_kunjungan_semua_keadaan_keluar_graph_pie/?md5=".md5(date("Ymdhis"))."\" alt=\"Pasien\" />";
		$objResponse->addAssign("graph", "innerHTML", $_SESSION[rekmed][statistik_kunjungan_semua_keadaan_keluar][graph]);
		return $objResponse;
	}

}

$_xajax->registerFunction(array("get_pasien_check", "Statistik_Kunjungan_Semua_Keadaan_Keluar", "get_pasien_check"));
$_xajax->registerFunction(array("get_pasien", "Statistik_Kunjungan_Semua_Keadaan_Keluar", "get_pasien"));
?>