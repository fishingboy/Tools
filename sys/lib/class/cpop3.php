<?php
/*
 * pop3.php
*/

class Pop3
{
	var $hostname = "";
	var $port = 110;
	var $quit_handshake = 0;

	/* Private variables - DO NOT ACCESS */

	var $connection = 0;
	var $state = "DISCONNECTED";
	var $greeting = "";
	var $must_update = 0;
	var $debug = 0;
	var $next_token = "";

	/* Private methods - DO NOT CALL */

	Function Tokenize($string, $separator="")
	{
		if(!strcmp($separator,""))
		{
			$separator = $string;
			$string = $this->next_token;
		}
		for($character=0; $character<strlen($separator); $character++)
		{
			if(GetType($position=strpos($string,$separator[$character])) == "integer")
				$found = (IsSet($found) ? min($found,$position) : $position);
		}
		if(IsSet($found))
		{
			$this->next_token = substr($string,$found+1);
			return(substr($string,0,$found));
		}
		else
		{
			$this->next_token = "";
			return($string);
		}
	}

	Function OutputDebug($message)
	{
		echo $message,"\n";
	}

	Function GetLine()
	{
		for($line = "";;)
		{
			if(feof($this->connection))
				return(0);
			$line .= fgets($this->connection, 100);
			$length = strlen($line);
			if($length>=2 && substr($line, $length-2, 2) == "\r\n")
			{
				$line = substr($line,0,$length-2);
				if($this->debug)
					$this->OutputDebug("< $line");
				return($line);
			}
		}
	}

	Function PutLine($line)
	{
		if($this->debug)
			$this->OutputDebug("> $line");
		return(fputs($this->connection, "$line\r\n"));
	}

	Function OpenConnection()
	{
		if($this->hostname == "")
			return("2 it was not specified a valid hostname");
			
		if($this->debug)
			$this->OutputDebug("Connecting to " . $this->hostname . " ...");
			
		if(($this->connection = @fsockopen($this->hostname, $this->port, $error, $errstr, 9)) == 0)
		{
			switch($error)
			{
				case -3:
					return("-3 socket could not be created");
				case -4:
					return("-4 dns lookup on hostname \"$hostname\" failed");
				case -5:
					return("-5 connection refused or timed out");
				case -6:
					return("-6 fdopen() call failed");
				case -7:
					return("-7 setvbuf() call failed");
				default:
					return($error . " could not connect to the host \"" . $this->hostname . "\"");
			}
		}
		return("");
	}

	Function CloseConnection()
	{
		if($this->debug)
			$this->OutputDebug("Closing connection.");
		if($this->connection != 0)
		{
			fclose($this->connection);
			$this->connection = 0;
		}
	}

	/* Public methods */

	/* Open method - set the object variable $hostname to the POP3 server address. */

	Function Open()
	{
		if($this->state != "DISCONNECTED")
			return("1 a connection is already opened");
			
		if(($error = $this->OpenConnection()) != "")  // some error occur
			return($error);
			
		$this->greeting = $this->GetLine();
		if(GetType($this->greeting) != "string" || $this->Tokenize($this->greeting," ") != "+OK")
		{
			$this->CloseConnection();
			return("3 POP3 server greeting was not found");
		}
		$this->Tokenize("<");
		$this->must_update = 0;
		$this->state = "AUTHORIZATION";
		return("");
	}
	
	/* Close method - this method must be called at least if there are any
     messages to be deleted */

	Function Close()
	{
		if($this->state == "DISCONNECTED")
			return("no connection was opened");
		if($this->must_update || $this->quit_handshake)
		{
			if($this->PutLine("QUIT") == 0)
				return("Could not send the QUIT command");
			$response = $this->GetLine();
			if(GetType($response) != "string")
				return("Could not get quit command response");
			if($this->Tokenize($response," ") != "+OK")
				return("Could not quit the connection: ".$this->Tokenize("\r\n"));
		}
		$this->CloseConnection();
		$this->state = "DISCONNECTED";
		return("");
	}

	/* Login method - pass the user name and password of POP account.  Set
     $apop to 1 or 0 wether you want to login using APOP method or not.  */

	Function Login($user, $password, $apop)
	{
		if($this->state != "AUTHORIZATION")
			return(-7);  //connection is not in AUTHORIZATION state
		if($apop)
		{
			if(!strcmp($this->greeting, ""))
				return(-7); //Server does not seem to support APOP authentication
			if($this->PutLine("APOP $user " . md5("<" . $this->greeting . ">" .$password)) == 0)
				return(-7); //Could not send the APOP command
				
			$response = $this->GetLine();
			if(GetType($response) != "string")
				return(-7); //Could not get APOP login command response
			if($this->Tokenize($response," ") != "+OK")
				return(-7);  //"APOP login failed: " . $this->Tokenize("\r\n")
		}
		else
		{
			if($this->PutLine("USER $user") == 0)
				return(-7);  //Could not send the USER command
				
			$response = $this->GetLine();
			if(GetType($response) != "string")
				return(-7); //Could not get user login entry response
			if($this->Tokenize($response," ") != "+OK")
				return(-5); //"User error: " . $this->Tokenize("\r\n")
			if($this->PutLine("PASS $password") == 0)
				return(-7); //Could not send the PASS command
				
			$response = $this->GetLine();
			if(GetType($response) != "string")
				return(-7);  //Could not get login password entry response
			if($this->Tokenize($response," ") != "+OK")
				return(-5); //"Password error: " . $this->Tokenize("\r\n")
		}
		$this->state = "TRANSACTION";
		return(0);
	}



};

?>