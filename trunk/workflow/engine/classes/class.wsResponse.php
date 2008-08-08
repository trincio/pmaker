<?php
class wsResponse
{
	public $status_code = 0;
	public $message = '';
	public $timestamp = '';
	
	function __construct( $status, $message ) {
		$this->status_code = $status;
		$this->message     = $message;
		$this->timestamp   = date('Y-m-d H:i:s');
	}
	
	function getPayloadString ( $operation ) {
		$res = "<$operation>\n";
		$res .= "<status_code>" . $this->status_code . "</status_code>";
		$res .= "<message>" . $this->message . "</message>";
		$res .= "<timestamp>" . $this->timestamp . "</timestamp>";
		$res .= "<array>" . $this->timestamp . "</array>";
		$res .= "<$operation>";
		return $res;
	}

	function getPayloadArray (  ) {
		return array("status_code" => $this->status_code , 'message'=> $this->message, 'timestamp' => $this->timestamp);
	}
}