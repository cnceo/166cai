<?php
class shop extends MY_Controller
{
	protected $_path;
	protected $_lotteryTypes;

	public function __construct()
	{
		parent::__construct ();
		$this->load->model ( 'cp_partner_shop', 'shop' );
		$this->_lotteryTypes = array (
				'0' => '体彩',
				'1' => '福彩' 
		);
		$this->_path = '../uploads/partner/' . $this->_pid;
	}

	public function index()
	{
		$page = $this->input->get ( 'page' );
		$page = empty ( $page ) ? 1 : $page;
		$shopNum = $this->input->get ( 'shopNum' );
		$data = $this->shop->getListData ( ($page - 1) * self::NUM_PER_PAGE, self::NUM_PER_PAGE, $this->_pid, $shopNum );
		$data ['shopNum'] = $shopNum;
		$this->load->library ( 'pagination' );
		$pageConfig ['total_rows'] = $data ['total'] ['num'];
		$pageConfig ['per_page'] = self::NUM_PER_PAGE;
		$pageConfig ['query_string_segment'] = 'page';
		$pageConfig ['use_page_numbers'] = TRUE;
		$pageConfig ['page_query_string'] = true;
		$pageConfig ['base_url'] = "shop?1";
		$pageConfig ['prev_link'] = '<i>&lt;</i>上一页';
		$pageConfig ['next_link'] = '下一页<i>&gt;</i>';
		$pageConfig ['prev_tag_open'] = '<span class="prev">';
		$pageConfig ['prev_tag_close'] = '</span>';
		$pageConfig ['next_tag_open'] = '<span class="next">';
		$pageConfig ['next_tag_close'] = '</span>';
		$pageConfig ['cur_tag_open'] = '<a class="cur">';
		$pageConfig ['cur_tag_close'] = '</a>';
		$this->pagination->initialize ( $pageConfig );
		$data ['pageStr'] = $this->pagination->create_links ();
		$data ['per_page'] = self::NUM_PER_PAGE;
		$this->display ( 'shop/index', $data );
	}

	public function edit()
	{
		$id = $this->input->get ( 'id' );
		$info = $this->input->post ( 'info' );
		$filename = $this->input->post ( 'filename' );
		$filepath = $this->input->post ( 'filepath' );
		$filedel = $this->input->post ( 'delfile' );
		$this->load->model ( 'cp_partner_shop_file', 'file' );
		$data = array ();
		if ($id)
		{
			$data ['data'] = $this->shop->getDataById ( $id );
			$data ['files'] = $this->file->getDataByShopId ( $id );
		}
		if ($info)
		{
			$delcount = empty($filedel) ? 0 : count(explode(',', $filedel));
			if ($data ['data'] ['status'] > 10)
			{
				$this->redirect ( $this->config->item ( 'base_url' ) . '/shop/detail?id=' . $id . '&noedit=1' );
			} else if (! empty ( $filename ) && count ( $data ['files'] ) + count ( $filename ) - $delcount > 5)
			{
				if ($id)
				{
					$data ['data'] = $this->shop->getDataById ( $id );
					$data ['files'] = $this->file->getDataByShopId ( $id );
					$data ['path'] = str_replace ( '../', '/', $this->_path );
				}
				$data ['lotteryTypes'] = $this->_lotteryTypes;
				
				$this->display ( 'shop/edit', $data );
				echo '<script>alert(\'文件数量超过5个！\')</script>';
			} else
			{
				
				foreach ($info as $k => $v)
				{
					$info[$k] = strip_tags($v);
				}
				
				$info ['partnerId'] = $this->_pid;
				$info ['partner_name'] = $this->_pname;
				$info ['status'] = 0;
				
				if ($id)
				{
					$this->shop->save ( $info, array (
							'id' => $id 
					) );
				} else
				{
					$id = $this->shop->save ( $info );
				}
				if ($filedel)
				{
					$this->delFile ( $filedel );
				}
				
				if (! empty ( $filename ))
				{
					foreach ( $filename as $k => $file )
					{
						$files [$k] ['filename'] = $file;
						$files [$k] ['filepath'] = $id . $filepath [$k];
						rename ( $this->_path . "/" . $filepath [$k], $this->_path . "/" . $id . $filepath [$k] );
					}
					$info ['shopId'] = $id;
					$this->file->saveAll ( $files, $info );
				}
				
				$this->redirect ( $this->config->item ( 'base_url' ) . '/shop/detail?id=' . $id );
			}
		} else
		{
			if ($id)
			{
				$data ['path'] = str_replace ( '../', '/', $this->_path );
			}
			$data ['lotteryTypes'] = $this->_lotteryTypes;
			$this->display ( 'shop/edit', $data );
		}
	}

	public function detail()
	{
		$noedit = $this->input->get ( 'noedit' );
		if ($noedit)
		{
			echo '<script>alert(\'投注站不可编辑！\')</script>';
		}
		$id = $this->input->get ( 'id' );
		$this->load->model ( 'cp_partner_shop_file', 'file' );
		$data ['data'] = $this->shop->getDataById ( $id );
		$data ['path'] = str_replace ( '../', '/', $this->_path );
		$data ['files'] = $this->file->getDataByShopId ( $id );
		$data ['lotteryTypes'] = $this->_lotteryTypes;
		$this->display ( 'shop/detail', $data );
	}

	public function upload()
	{
		$id = $this->input->get ( 'id' );
		if ($id)
		{
			$this->load->model ( 'cp_partner_shop_file', 'file' );
			$fid = $this->file->getFileByName ( $id, $_FILES ['file'] ['name'] );
			if ($this->file->getFileByName ( $id, $_FILES ['file'] ['name'] ))
			{
				exit ( json_encode ( $fid ) );
			}
		}
		
		if (! file_exists ( '../uploads/partner/' ))
		{
			mkdir ( '../uploads/partner/' );
		}
		if (! file_exists ( $this->_path ))
		{
			mkdir ( $this->_path . "/" );
		}
		// print_r($_FILES);
		$config ['upload_path'] = $this->_path . "/";
		$config ['allowed_types'] = 'zip|jpg|png|bmp|jpeg|txt|pdf|doc|docx|xls|xlsx|tar';
		$extension = pathinfo ( $_FILES ['file'] ['name'], PATHINFO_EXTENSION );
		
		$config ['file_name'] = "_" . time () . rand ( 0001, 9999 ) . "." . $extension;
		$config ['max_size'] = 10240;
		$this->load->library ( 'upload', $config );
		
		if ($this->upload->do_upload ( 'file' ))
		{
			$data = $this->upload->data ();
			$res = array (
					'filepath' => $data ['file_name'],
					'filename' => $data ['client_name'] 
			);
			exit ( json_encode ( $res ) );
		} else
		{
			$error = $this->upload->display_errors ();
			exit ( $error );
		}
	}

	public function delFile($ids)
	{
		$this->load->model ( 'cp_partner_shop_file', 'file' );
		$this->file->save ( array (
				'delete_flag' => '1' 
		), "id in (" . $ids . ")" );
	}

	public function checkShopNum()
	{
		$id = $this->input->get ( 'id' );
		$shopnum = $this->input->get ( 'shopnum' );
		if ($this->shop->checkShopnum ( $this->_pid, $shopnum, $id ))
		{
			exit ( '1' );
		}
	}

	public function download()
	{
		$file = $this->_path . "/" . $this->input->get ( 'filepath' );
		header ( "Content-type: application/octet-stream" );
		header ( 'Content-Disposition: attachment; filename="' . basename ( $file ) . '"' );
		header ( "Content-Length: " . filesize ( $file ) );
		readfile ( $file );
	}
}