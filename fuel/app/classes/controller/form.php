<?php

class Controller_Form extends Controller_Template {
	
	public function action_index() {
		$this->template->title = 'コンタクトフォーム';
		$this->template->content = View::forge('form/index');
	}

}