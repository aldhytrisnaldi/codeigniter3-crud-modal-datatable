<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('produk_model','produk');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('produk_view');
	}

	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->produk->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $produk) {
			$no++;
			$row = array();
			$row[] = $produk->id_produk;
			$row[] = $produk->id_kategori;
			$row[] = $produk->nama_produk;
			$row[] = $produk->kode_produk;
			if($produk->foto_produk)
				$row[] = '<a href="'.base_url('upload/'.$produk->foto_produk).'" target="_blank"><img src="'.base_url('upload/'.$produk->foto_produk).'" class="img-responsive" width="50px"/></a>';
			else
				$row[] = '(Tidak ada Foto)';
			$row[] = $produk->tgl_register;
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update" onclick="edit_produk('."'".$produk->id_produk."'".')">Update</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_produk('."'".$produk->id_produk."'".')">Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->produk->count_all(),
			"recordsFiltered" => $this->produk->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}
	
	public function ajax_add()
	{
		$this->_validate();
		$data = array(
			'nama_produk' 	=> $this->input->post('nama_produk'	),
			'kode_produk' 	=> $this->input->post('kode_produk'),	
			'id_kategori' 	=> $this->input->post('id_kategori'	),
			'tgl_register' 	=> $this->input->post('tgl_register'),
		);
		if(!empty($_FILES['photo']['name']))
		{
			$upload = $this->_do_upload();
			$data['foto_produk'] = $upload;
		}
		$insert = $this->produk->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->produk->get_by_id($id);
		$data->tgl_register = ($data->tgl_register == '0000-00-00') ? '' : $data->tgl_register;
		echo json_encode($data);
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
			'nama_produk' 	=> $this->input->post('nama_produk'	),
			'kode_produk' 	=> $this->input->post('kode_produk'),	
			'id_kategori' 	=> $this->input->post('id_kategori'	),
			'tgl_register' 	=> $this->input->post('tgl_register'),
		);

		if($this->input->post('remove_photo'))
		{
			if(file_exists('upload/'.$this->input->post('remove_photo')) && $this->input->post('remove_photo'))
				unlink('upload/'.$this->input->post('remove_photo'));
			$data['foto_produk'] = '';
		}

		if(!empty($_FILES['photo']['name']))
		{
			$upload = $this->_do_upload();
			
			//delete file
			$produk = $this->produk->get_by_id($this->input->post('id_produk'));
			if(file_exists('upload/'.$produk->foto_produk) && $produk->foto_produk)
				unlink('upload/'.$produk->foto_produk);

			$data['foto_produk'] = $upload;
		}

		$this->produk->update(array('id_produk' => $this->input->post('id_produk')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$produk = $this->produk->get_by_id($id);
		if(file_exists('upload/'.$produk->foto_produk) && $produk->foto_produk)
			unlink('upload/'.$produk->foto_produk);
		
		$this->produk->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

	private function _do_upload()
	{
		$config['upload_path']          = 'upload/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 200;
        $config['max_width']            = 1000;
        $config['max_height']           = 1000; 
        $config['file_name']            = round(microtime(true) * 1000);
        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('photo'))
        {
            $data['inputerror'][] = 'photo';
			$data['error_string'][] = 'Upload error: '.$this->upload->display_errors('','');
			$data['status'] = FALSE;
			echo json_encode($data);
			exit();
		}
		return $this->upload->data('file_name');
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nama_produk') == '')
		{
			$data['inputerror'][] = 'nama_produk';
			$data['error_string'][] = 'Nama produk harus di isi';
			$data['status'] = FALSE;
		}

		if($this->input->post('kode_produk') == '')
		{
			$data['inputerror'][] = 'kode_produk';
			$data['error_string'][] = 'Kode produk harus di isi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_register') == '')
		{
			$data['inputerror'][] = 'tgl_register';
			$data['error_string'][] = 'Tanggal produk harus di isi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
