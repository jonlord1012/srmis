function focusFirst() {
	document.getElementById('jangka_waktu').focus();
	document.getElementById('jangka_waktu').select;
	xajax_get_pasien_check(xajax.getFormValues('statistik_kunjungan_semua_keadaan_keluar'));
}
function tutup_list_pasien() {
	document.getElementById('list_pasien').style.display='none';
}
function setDisable(obj) {
	if(obj.value == "tahun") {
		document.getElementById('tgl_periksa_tgl_start').disabled = true;
		document.getElementById('tgl_periksa_tgl_end').disabled = true;
		document.getElementById('tgl_periksa_bln_start').disabled = true;
		document.getElementById('tgl_periksa_bln_end').disabled = true;
	} else if (obj.value == "bulan")
	{
		document.getElementById('tgl_periksa_tgl_start').disabled = true;
		document.getElementById('tgl_periksa_tgl_end').disabled = true;
		document.getElementById('tgl_periksa_bln_start').disabled = false;
		document.getElementById('tgl_periksa_bln_end').disabled = false;
	} else {
		document.getElementById('tgl_periksa_tgl_start').disabled = false;
		document.getElementById('tgl_periksa_tgl_end').disabled = false;
		document.getElementById('tgl_periksa_bln_start').disabled = false;
		document.getElementById('tgl_periksa_bln_end').disabled = false;
	}
}