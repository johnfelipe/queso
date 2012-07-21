<?php

class Quest extends Common_Auth_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('quest_model');
		$this->load->model('skill_model');
	}
	
	public function index() {
		$data['quests'] = $this->quest_model->get_quests();
		$data['title'] = "Quests";
		$this->load->view('include/header');
		$this->load->view('quests/index', $data);
      	$this->load->view('include/footer');
	}
	
	public function responses() {
		$data['title'] = "Completed Quests";
		$this->load->model('submission_model');
		$quests = $this->quest_model->get_completed_quests($this->the_user->user_id);		
	
	
	}
	
	public function completed() {
		//student view
		$data['title'] = "Completed Quests";
		$this->load->model('submission_model');
		$this->load->model('skill_model');
		$quests = $this->quest_model->get_completed_quests($this->the_user->user_id);		
		foreach ($quests as $quest) {
			if ($quest) {
			//submission
				$sid = $this->quest_model->get_latest_submission_id($quest->qid, $this->the_user->user_id);
			}
			else {
				$sid = 0;
			}
			$questBestPossible = $this->quest_model->get_quest_skills($quest->qid, TRUE);
				foreach ($questBestPossible as $bestSkill) {
					//compare to current progress
					$current = $this->quest_model->current_progress($bestSkill[0]->skid, $quest->qid, $this->the_user->user_id);
					if ($current) {
						$progress = array(
								'skill' => $current['name'],
								'amount' => $current['amount'],
								'id' => $current['skid'],
								'total' => $bestSkill[0]->amount,
								'percentage' => ($current['amount'] / $bestSkill[0]->amount) * 100,
								);
						$questProgress[] = $progress;
					}							
				}
				$data['quests'][] = array(
									'quest' => $quest,
									'submission' => $sid,
									'progress' => $questProgress);
				unset($questProgress);
			}
		$data['summary'] = $this->skill_model->get_total_by_user($this->the_user->user_id);
		$this->load->view('include/header');
		$this->load->view('quests/completed', $data);
		$this->load->view('include/footer');
		
	}
	
	public function attempt($id = NULL) {
		$this->load->helper('form');
		$this->load->library('form_validation');
		if ($id != 'post') {
			$info = $this->quest_model->get_quests($id);
//			$submission = $this->submission_model->get_attempt($id);
			$data['title'] = $info['name'];
			$data['id'] = $info['id'];
			$data['instructions'] = $info['instructions'];
// TODO: Update these to get dynamic data
			$data['grade'] = "First Try";
			$data['attempt'] = "0";

			$this->load->view('include/header');
			$this->load->view('quests/attempt', $data);
			$this->load->view('include/footer');
		}
		else  {
		//TODO: check if id is valid
			$this->load->model('submission_model');

			$id = $this->submission_model->submit($this->the_user->user_id);
			redirect('/submission/'.$id, 'location');
		
		}
		
	}
	

	public function available($qtype = 'all', $user = '0') {
		//student view
		$data['title'] = "Available Quests";
		if ($qtype == 'all') {
			$data['quests'] = $this->quest_model->get_quests();
		}
		else if ($qtype == 'online') {
			$data['quests'] = $this->quest_model->get_available_quests(2, $this->the_user->user_id);
		}
		
		else if ($qtype == 'in-class') {
			$data['quests'] = $this->quest_model->get_available_quests(1, $this->the_user->user_id);
		}
		$this->load->view('include/header');
		$this->load->view('quests/available', $data);
		$this->load->view('include/footer');
		
	}
	
	
	
	public function view($id) {
		//student view
		$data['quests'] = $this->quest_model->get_quests($id);
		
		if (empty($data['quests'])) {
			show_404();
		}
		
		$data['title'] = $data['quests']['name'];
		
		$this->load->view('include/header');
		$this->load->view('quests/view', $data);
      	$this->load->view('include/footer');
	}
}