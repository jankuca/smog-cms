<?php
class SQLite
{
	public $handle;		// The database handle
	protected $prefix;	// The tables prefix
	
	public function __construct($filename,$prefix,$mode = 0777)
	{
		# @param string $filename
		# @param string $prefix
		# [@param int $mode]
		
		$this->handle = sqlite_open((string) $filename,(int) $mode);	// Open the database file
		$this->prefix = (string) $prefix;	// Set the prefix
	}
	
	public function table($tablename,$prefix = false)
	{
		# This method returns full table name (with prefix).
		# @return string
		
		if(!$prefix) $prefix = $this->prefix;
		return((string) $prefix . $tablename);
	}
}

class SQLObject
{
	private $resource;	// Contains the last resource (result of a query).
	public $error;			// Contains the last error message.
	
	public function table($tablename,$prefix = false)
	{
		# This method returns full table name (with prefix).
		# @param string $tablename
		# [@param string $prefix]
		# @return string
		
		global $sqlite;
		if(!$prefix) $prefix = '';
		return((string) $sqlite->table($tablename,$prefix));
	}
	
	public function escape($string)
	{
		return(str_replace('\'','\'\'',$string));
	}
	
	public function exec($query)
	{
		# This method executes a query.
		# @param string $query
		# @return boolean
		
		global $sqlite,$syslog;
		ob_start();		// Start catching error messages
		if(sqlite_exec($sqlite->handle,$query))
		{
			ob_end_clean();
			$syslog->success('SQLObject','exec()',$query);
			return(true);
		}
		else
		{
			$this->error = strip_tags(ob_get_contents(),'<strong><b>');		// Get the error message from the buffer
			$this->sqlite_error = sqlite_error_string(sqlite_last_error($sqlite->handle));	// Get the error message of the error code
			ob_end_clean();
			$syslog->error('SQLObject','exec()',$query,$this->sqlite_error);
			return(false);
		}
	}
	
	public function query($query)
	{
		# This method executed a query and returns (SQLiteResult) $this->resource.
		# @param string $query
		# @return boolean
		
		global $sqlite,$syslog;
		ob_start();		// Start catching error messages
		if($this->resource = sqlite_query($sqlite->handle,$query))
		{
			ob_end_clean();
			$syslog->success('SQLObject','query()',$query);
			return(true);
		}
		else
		{
			$this->error = strip_tags(ob_get_contents(),'<strong><b>');		// Get the error message from the buffer
			$this->sqlite_error = sqlite_error_string(sqlite_last_error($sqlite->handle));	// Get the error message of the error code
			ob_end_clean();
			$syslog->error('SQLObject','query()',$query,$this->sqlite_error . '<br />' . $this->error);
			return(false);
		}
	}
	
	public function fetch()
	{
		# This method fetches all items in resource as objects and returns an array containing these objects.
		# @return array
		
		$results = array();
		while($result = sqlite_fetch_object($this->resource)) $results[] = $result;
		return((array) $results);
	}
	public function fetch_one()
	{
		return(sqlite_fetch_object($this->resource));
	}
	
	public function num_rows()
	{
		return(sqlite_num_rows($this->resource));
	}
	
	public function last_insert_id()
	{
		global $sqlite;
		return(sqlite_last_insert_rowid($sqlite->handle));
	}
}
?>