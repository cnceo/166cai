<?php
class Mdownload extends MY_Controller
{

	public function index()
	{
		$file = FCPATH."source/download/m/166cp.exe";
		header ( "Content-type: application/octet-stream" );
		Header( "Accept-Ranges:  bytes ");
		header ( 'Content-Disposition: attachment; filename=166cp.exe' );
		header ( "Content-Length: " . filesize ( $file ) );
		readfile ( $file );
	}

}