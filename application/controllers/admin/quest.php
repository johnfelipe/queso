<?php

class Quest extends Admin_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('quest_model');
		$this->load->model('skill_model');
	}
	
	public function index() {
		$data['quests'] = $this->quest_model->get_quests();
		$data['title'] = "Quests";
		$this->load->view('include/header', $data);
		$this->load->view('quests/admin_index', $data);
      	$this->load->view('include/footer');
	}
	
	public function details($qid) {
		$info = $this->quest_model->get_quest_details($qid);
		$data['details'] = $info['details'];
		$data['students'] = $info['students'];
		$this->load->view('include/header');
		$this->load->view('quests/details', $data);
		$this->load->view('include/footer');
		
	}
	
	public function reorder() {
		$this->quest_model->reorder();
	}
	
	public function rmfile($id) {
		$this->quest_model->remove_file($id);
	}
	public function edit($qid) {
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('quest-title', 'Quest Name', 'required');
		$this->form_validation->set_rules('quest-instructions', 'Instructions', 'required');

		if ($this->form_validation->run() === FALSE) {
		}

		else {
		
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|pdf|zip|doc|docx|odf';
			$this->load->library('upload', $config);
			$uploaded = $this->upload->do_upload();
			
			$hasFile = FALSE;
			if ($uploaded) {
				$data = array('upload_data' => $this->upload->data());
				$hasFile = TRUE;
			}

				
			$this->quest_model->update($qid, $hasFile);
			redirect(base_url("admin/quests"));
		}

		$info = $this->quest_model->get_quests($qid);
		$this->load->model('grade_model');
		$data['grades'] = $this->grade_model->get_grades("ASC");
		$data['title'] = $info['name'];
		$data['id'] = $qid;
		$data['file'] = $info['file'];
		$data['instructions'] = $info['instructions'];
		$data['skills'] = $this->skill_model->get_skills();
		$data['locks'] = $this->quest_model->get_quest_locks($qid);
		$data['quest_skills'] = $this->quest_model->get_quest_skills($qid);
		$this->load->view('include/header');
		$this->load->view('quests/edit', $data);
		$this->load->view('include/footer');
		
	}


	public function remove_student($qid, $uid) {
		$this->quest_model->remove_quest_for_student($qid, $uid);
		redirect(base_url("admin/quest/details/".$qid), 'refresh');
	}
	
	public function create() {
		//admin
		$this->load->helper('form');
		$data['title'] = "Create a Quest";
		$data['instructions'] = "";
		$data['options'] = $this->quest_model->get_quest_types();
		$data['skills'] = $this->skill_model->get_skills();
		$this->load->view('include/header');
		$this->load->view('quests/create', $data);
		$this->load->view('include/footer');
		
	}
	

	public function skills() {
		$this->load->helper('form');
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|pdf|zip|doc|docx|odf';

		$this->load->library('upload', $config);
		
			if (! $this->upload->do_upload()) {
				$error = array('error' => $this->upload->display_errors());
			}
			else {
				$data = array('upload_data' => $this->upload->data());
			}
			$info = $this->quest_model->new_quest(TRUE);
		
		$data['title'] = $info['name'];
		$data['requirements'] = $info['requirements'];
		$data['id'] = $info['id'];
		$data['instructions'] = "You will be able to give your students points in each skill you selected.  If you add more than one amount for a given skill, you can choose what level you would like to reward your student with when grading their work.";
		
		foreach ($info['skills'] as $sid) {
			$data['skills'][] = $this->skill_model->get_skills($sid);	
		}
		
		$this->load->view('include/header');
		$this->load->view('quests/skills', $data);	
		$this->load->view('include/footer');

	}
	
	public function activate($qid) {
		$this->quest_model->show($qid);
		redirect("admin/quests", 'refresh');

	}
	
	public function deactivate($qid) {
		$this->quest_model->hide($qid);
		redirect("admin/quests", 'refresh');
	
	}

	public function remove($qid) {
		$this->quest_model->remove_quest($qid);
		redirect("admin/quests", 'refresh');

	}
	
	function grade($qtype = 'all', $qid = NULL, $uid = NULL, $rid = NULL) {
		$this->load->helper('form');
		$this->load->model('user_model');
		$this->load->library('form_validation');
		if ($qid != NULL) {
			$data['selected'] = $qid;
		}
		if ($uid != NULL) {
			$data['uid'] = $uid;
		}
		
		if ($rid != NULL) {
			$data['rid'] = $rid;
		}
		
		if ($qtype == 'in-class') {
			$data['title'] = "Grade In Class Quest";
			$data['quests'] = $this->quest_model->get_available_quests(1);
		}
		else if ($qtype == 'online') {
			$data['title'] = "Grade Submitted Quest";
			$data['quests'] = $this->quest_model->get_available_quests(2);
		
		}
		
		else if ($qtype == 'all') {
			$data['title'] = "Grade All Kinds of Quests";
			
			$all_quests = $this->quest_model->get_quests();
			foreach($all_quests as $quest) {
				$data['quests'][] = array(
				'info' => $quest,				
				);
			}

		}
		
		if ($qtype != 'post') {		
			$data['users'] = $this->user_model->get_info();
			$this->load->view('include/header');
			$this->load->view('quests/grade', $data);	
			$this->load->view('include/footer');
		}
		else {
			$this->quest_model->complete_quest();
			redirect(base_url('admin/quest/grade/in-class'), 'location');
		}
	}
	
	public function skill_rewards() {
		$skills = $this->quest_model->ajax_quest_skills();
		foreach ($skills as $skill) {
				echo '<div class="control-group"><label class="control-label" for="skill-type">'.$skill[0]->name.'</label><div class="controls"><input type="hidden" name="skill[]" value="'.$skill[0]->skid.'"><select name="award[]"class="chzn-select">';
				foreach ($skill as $option) {
					echo '<option value="'.$option->amount.'">'.$option->label.'</option>';
				}
				echo '</select></div></div>';

		}
	}

	function confirm() {
	
		$info = $this->quest_model->confirm_quest();
		$data['title'] = $info['name'];
		$data['id'] = $info['id'];
		$data['requirements'] = $info['requirements'];
		/*
		if ($info['requirements']) {
			$data['qtypes'] = $this->quest_model->get_quest_types();
		}
		*/
		$this->load->view('include/header');
		$this->load->view('quests/confirmation', $data);
		$this->load->view('include/footer');
		
		
	}
	
		
		
	public function available($qtype = 'all', $user = '0') {
		//admin view
		$data['title'] = "Available Quests";
		if ($qtype == 'all') {
			$data['quests'] = $this->quest_model->get_quests();
		}
		else if ($qtype == 'online') {
			$data['quests'] = $this->quest_model->get_available_quests(2, $user);
		}
		
		else if ($qtype == 'in-class') {
			$data['quests'] = $this->quest_model->get_available_quests(1, $user);
		}
		$this->load->view('include/header');
		$this->load->view('quests/available', $data);
		$this->load->view('include/footer');
		
	}
	
	
}