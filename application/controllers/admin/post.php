<?php

class Post extends Admin_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('post_model');

	}
		
	public function create() {
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('headline', 'Headline', 'required');
		$this->form_validation->set_rules('body', 'Body', 'required');
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|pdf|zip|doc|docx|odf';
		$this->load->library('upload', $config);
		
		$data['title'] = "Create Post";
		if ($this->form_validation->run() === FALSE) {}

		else {
			if (! $this->upload->do_upload()) {
				$error = array('error' => $this->upload->display_errors());
			}
			else {
				$data = array('upload_data' => $this->upload->data());
			}

			$id = $this->post_model->submit();
			redirect("post/".$id);
		}
		
		$this->load->view('include/header');
		$this->load->view('posts/create');
      	$this->load->view('include/footer');

	}
	
	public function index() {
		$data['posts'] = $this->post_model->get_posts();
		$data['title'] = "Posts";
		$this->load->view('include/header');
		$this->load->view('posts/admin_index', $data);
      	$this->load->view('include/footer');

	}
	
	public function edit($id) {
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('headline', 'Headline', 'required');
		$this->form_validation->set_rules('body', 'Body', 'required');
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|pdf|zip|doc|docx|odf';
		$this->load->library('upload', $config);
		$uploaded = $this->upload->do_upload();
		
		$hasFile = FALSE;
		if ($uploaded) {
			$data = array('upload_data' => $this->upload->data());
			$hasFile = TRUE;
		}


		if ($this->form_validation->run() === FALSE) {}

		else {
			$this->post_model->update($hasFile);
			redirect("post/".$id);
		}

		$info = $this->post_model->get_post($id);
		$data['title'] = $info['headline'];
		$data['body'] = $info['body'];
		$data['frontpage'] = $info['frontpage'];
		$data['pid'] = $info['id'];
		$data['file'] = $info['file'];
		$this->load->view('include/header');
		$this->load->view('posts/edit', $data);
      	$this->load->view('include/footer');
	
	}
	
	public function rmfile($id) {
		$this->post_model->remove_file($id);
	}
	
	public function reorder() {
		$this->post_model->reorder();
	}
	
	public function delete($id) {
		$id = $this->post_model->remove($id);
		redirect("/admin/posts");

	}
	
	public function removemenu($id) {
		$id = $this->post_model->removeFromMenu($id);
		redirect("/admin/posts");
	
	}
	
	public function addmenu($id) {
		$id = $this->post_model->addToMenu($id);
		redirect("/admin/posts");
	
	}
		
	public function promote($id) {
		$id = $this->post_model->promote($id);
		redirect("/admin/posts");
	}
	
	public function demote($id) {
		$id = $this->post_model->demote($id);
		redirect("/admin/posts");

	}
}