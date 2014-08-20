<?php
namespace Reshetech\MyApi;

class Views
{
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_FOUND = 302;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const HTTP_LOCKED = 423;                                                      // RFC491
    const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585
	
	/**
	 * Header code.
	 *
	 * @var int
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
	 * @var bool
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
	 * @param  mixed $message
	 * @return $this
	 */	
	public function unAuthorized($message=null)
	{
	    $this->code      = self::HTTP_FORBIDDEN;
		
		$this->message[] = ($message && is_string($message))?
		    Utilis::cleanString($message) : 
			"Unauthorized user.";
			
		$this -> setJsonHeader();
		
		return $this;
	}
	
	
	/**
	 * Set not found 404 header.
	 *
	 * @param  mixed $message
	 * @return $this
	 */
	public function notFound($message=null)
	{
		$this->code      = self::HTTP_NOT_FOUND;
		
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
	 * @return $this
	 */
	public function internalError($message=null)
	{
	    $this->code      = self::HTTP_INTERNAL_SERVER_ERROR;
		
		$this->message[] = ($message && is_string($message))?
		    Utilis::cleanString($message) : 
			"Internal server error.";
			
		$this -> setJsonHeader();
		
		return $this;
	}
	

	/**
	 * Set 200 header.
	 *
	 * @return $this
	 */
	public function ok()
	{
	    $this->code    = self::HTTP_OK;
		
		return $this;
	}
	
	
	/**
	 * Set header response.
	 *
	 * @param  string $message
	 * @param  int    $code
	 * @param  bool   $replace
	 * @return $this
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
	 * @return $this
	 */
	public function setJsonHeader()
	{		
		$this->setContent('application/json');
		
		return $this;
	}
	
	
	/**
	 * Set Xml header.
	 *
	 * @return $this
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
	 * 
	 * @param string $str
	 * @param bool   $exit 
	 */
	public function writeToScreen($str,$exit=true)
	{
	    if(is_string($str))
		    echo $str;
			
		if($exit)
		    exit;
	}
}