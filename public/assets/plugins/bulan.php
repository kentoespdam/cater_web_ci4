<?php
	function bulan($bulan)
	{
		Switch ($bulan){
			case 01 : $bulan="Januari";
			Break;
			case 02 : $bulan="Februari";
			Break;
			case 03 : $bulan="Maret";
			Break;
			case 04 : $bulan="April";
			Break;
			case 05 : $bulan="Mei";
			Break;
			case 06 : $bulan="Juni";
			Break;
			case 07 : $bulan="Juli";
			Break;
			case 08 : $bulan="Agustus";
			Break;
			case 09 : $bulan="September";
			Break;
			case 10 : $bulan="Oktober";
			Break;
			case 11 : $bulan="November";
			Break;
			case 12 : $bulan="Desember";
			Break;
		}
		return $bulan;
	}
?>