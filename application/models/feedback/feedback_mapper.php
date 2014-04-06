<?php
/*
 * Model of feedback mapper
 *
 * @author rav <arudyuk@gmail.com>
 * @version 1.0
 */

class Feedback_mapper extends MY_Model {
	protected $_template	= 'feedback/order';
	protected $_template_ok = 'feedback/ok';

	public function  __construct() {
		parent::__construct();
		$this->load->library('email');
	}

	public function get_page_content($page_id = 0) {
		$this->form_validation->set_rules('name', '<strong>имя</strong>', 'xss_clean|trim|required|min_length[2]|max_length[255]');
		$this->form_validation->set_rules('email', '<strong>e-mail</strong>', 'required|valid_email|min_length[4]|max_length[255]');
		$this->form_validation->set_rules('message', '<strong>сообщение</strong>', 'xss_clean|trim|required|min_length[8]|max_length[1000]');
		$this->form_validation->set_message('required', '<span>поле %s обязательно для заполнения</span><br/>');
		$this->form_validation->set_message('min_length', '<span>поле %s должно содержать больше символов</span><br/>');
		$this->form_validation->set_message('max_length', '<span>поле %s превысило максимальную длину</span><br/>');
		$this->form_validation->set_message('valid_email', '<span>поле <strong>e-mail</strong> имеет неверный формат</span><br/>');
		$this->form_validation->set_error_delimiters('', '');
		if ( ! empty($_POST) )
		{
			if ( $this->input->post('phone') )
			{
				$this->_check_phone_number($this->input->post('phone'));
			}
			if ( $this->form_validation->run() )
			{
				$this->_send_mail($this->input->post('name'), $this->input->post('email'), $this->input->post('phone'), $this->input->post('message'));
				return $this->_view_content($this->_template_ok, null, true);
			} else {
				return $this->_view_content($this->_template, null, true);
			}
		} else {
			return $this->_view_content($this->_template, null, true);
		}
	}

	protected function _check_phone_number ( $phone ) {
		if ( ! preg_match("/^(\+?\d+)?\s*(\(\d+\))?[\s-]*([\d-]*)$/", $phone) )
		{
			// $this->form_validation->set_errors(array('phone' => 'поле <strong>номер телефона</strong> имеет неверный формат'));
			$this->form_validation->set_message('phone', 'поле <strong>номер телефона</strong> имеет неверный формат');
		}
	}

	protected function _send_mail(
									$name,
									$email,
									$phone,
									$message = ''
								 ){
		$sql		= "SELECT name, value FROM settings WHERE name= 'EMAIL' OR name = 'MY_EMAIL'";
		$result		= $this->db->query($sql);
		$mail_array	= $result->result_array();
		foreach ($mail_array as $row) {
			$$row['name'] = $row['value'];
		}

		$msg  = "";
		$msg .= "имя: {$name} \n";
		if ( $phone )
		{
			$msg .= "телефон: {$phone} \n";
		}
		$msg .= "e-mail: {$email} \n";
		$msg .= "сообщение: {$message} \n";

		$this->email->from(FEEDBACK_FROM_EMAIL, FEEDBACK_FROM_NAME);
		$this->email->to($MY_EMAIL);
		$this->email->subject(FEEDBACK_SUBJECT);
		$this->email->message($msg);
		$this->email->send();
	}

}
