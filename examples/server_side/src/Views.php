<?php
namespace Reshetech\MyApi;

class Views
{
    /**
	 * Header code.
	 *
	 * @var integer
	 */	
	protected $code    = 200;
	
	/**
	 * Header text.
	 *
	 * @var array
	 */	
	protected $message = array();
	
	/**
	 * Header replace.
	 *
	 * @var boolean
	 */	
	protected $replace = true;
	
	/**
	 * Header content.
	 *
	 * @var mixed
	 */
	protected $content = null;
	
	
	/**
	 * Set unauthorized 403 header.
	 *
	 * @param  string $message
	 * @return Views
	 */	
	public function unAuthorized($message=null)
	{
	    $this->code      = 403;
		
		$this->message[] = ($message && is_string($message))?
		    Utilis::cleanString($message) : 
			"Unauthorized user.";
			
		$this -> setJsonHeader();
		
		return $this;
	}
	
	
	/**
	 * Set not found 404 header.
	 *
	 * @param  string $message
	 * @return Views
	 */
	public function notFound($message=null)
	{
	    $this->code      = 404;
		
		$this->message[] = ($message && is_string($message))?
		    Utilis::cleanString($message) : 
			"Resource not found.";
			
		$this -> setJsonHeader();
		
		return $this;
	}
	
	
	/**
	 * Set internal error 500 header.
	 *
	 * @param  string $message
	 * @return Views
	 */
	public function internalError($message=null)
	{
	    $this->code      = 500;
		
		$this->message[] = ($message && is_string($message))?
		    Utilis::cleanString($message) : 
			"Internal server error.";
			
		$this -> setJsonHeader();
		
		return $this;
	}
	

	/**
	 * Set 200 header.
	 *
	 * @return Views
	 */
	public function ok()
	{
	    $this->code    = 200;
		
		return $this;
	}
	
	
	/**
	 * Set header response.
	 *
	 * @param  string  $message
	 * @param  integer $code
	 * @param  boolean $replace
	 * @return Views
	 */
	public function setHeader($message,$code,$replace)
	{
	    if(is_string($message))
		    $this->message[] = Utilis::cleanString($message);
		
		if((int)$code>=100 || (int)$code<=600)
		    $this->code = (int)$code;
		
		if(is_bool($replace))
		    $this->replace = $replace;
			
		return $this;
	}
	
	
	/**
	 * Set Json header.
	 *
	 * @return Views
	 */
	public function setJsonHeader()
	{		
		$this->setContent('application/json');
		
		return $this;
	}
	
	
	/**
	 * Set Xml header.
	 *
	 * @return Views
	 */
	public function setXmlHeader()
	{
	    $this->setContent('application/xml');
		
		return $this;
	}
	
	
	/**
	 * Set the content type for the header.
	 *
	 * @param string $str
	 */
	public function setContent($str)
	{
	    if($str && is_string($str))
		    return $this->content = trim($str);
	}
	
	
	/**
	 * Get the content type for the header.
	 *
	 * @return mixed
	 */
	public function getContent()
	{
	    if($this->content && is_string($this->content))
		    return 'Content-Type: '.$this->content;
			
		return false;
	}
	
	
	/**
	 * Return header.
	 *
	 * @return header
	 */
	public function getHeader()
	{
        $protocol = (isset($_SERVER['SERVER_PROTOCOL'])? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		
		$code     = (int)$this->code;
		
		$message  = Utilis::arrayToString($this->message);
		
		$replace  = $this->replace;
		
		if($this->getContent())
		    header($this->getContent());
		
		return header($protocol . ' ' . $code . ' ' . $message, $replace);
	}
	
	
	/**
	 * Print string to screen, and exit.
	 */
	public function writeToScreen($str,$exit=true)
	{
	    if(is_string($str))
		    echo $str;
			
		if($exit)
		    exit;
	}
}