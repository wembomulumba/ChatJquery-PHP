<?php

	/*************************************************************************
	*	jChat
	*
	*	Author: Paulo Regina
	*	Version: 1.5
	*	
	**************************************************************************/
	
	class jChat 
	{
		############################################################################################### 
		#### Properties
		###############################################################################################
		
		public $clientID;
		public $client;
		public $serverID;
		public $server;
		public $attachmentPath;
		public $emoticons;
				
		// users table from same database
		public $users_table;
		public $users_usernameField;
		public $user_idField;
				
		############################################################################################### 
		#### Methods
		###############################################################################################
		
		/******************************************************************
		* Read Context
		*******************************************************************/
		
		public function __construct() 
		{
			// Set Internal Variables
			$this->table =TABLE;	

			$this->connection = new connectMe(DB_HOST, DB_USERNAME, DB_PASSWORD, DATABASE);
			
			$this->result = $this->connection->query("SELECT * FROM $this->table ");
		}
		
		///////////////////////////////////////////
		// Sanitize Integer
		/////////////////////////////////////////
		public function sanitize_integer($get_id)
		{
			return $this->connection->sanitize_integer($get_id);
		}
		
		/////////////////////////////////
		// Results Transformation
		/////////////////////////////////
		private function results($result)
		{
			return $this->connection->results($result);
		}
		
		///////////////////////////////////////////////
		// Time Calcuation
		//////////////////////////////////////////////
		private function time_calculation($timestamp)
		{
			$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");

			$lengths = array(60,60,24,7,4.35,12,10);

			$time = strtotime($timestamp);
			
			$now = time();
			
			$difference = $now - $time;	
			$tense = "ago";
			
			for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) 
			{
			   $difference /= $lengths[$j];
			}
			
			$difference = round($difference);
			
			if($difference != 1) 
			{
			   $periods[$j] .= "s";
			}
			
			$string_result = "$difference $periods[$j] $tense";
			
			return $string_result;	
		}
		
		////////////////////////////////////
		// Get Site Title
		//////////////////////////////////
		private function get_title($url)
		{
			$str = file_get_contents($url);
			if(strlen($str) > 0)
			{
				preg_match("/\<title\>(.*)\<\/title\>/", $str, $title);
				return $title[1];
			}
		}
		
		//////////////////////////////////////////////////////
		// Get Ids os the messages
		////////////////////////////////////////////////////
		public function get_messages_id($sender, $receiver)
		{
			// Result Query
			$this->result = $this->connection->query(
				sprintf(
						"SELECT id, user_id, receiver, storage_a, storage_b FROM $this->table
						WHERE user_id = %s AND receiver = %s 
						|| user_id = %s AND receiver = %s",
						$this->connection->escape($sender), 
						$this->connection->escape($receiver),
						$this->connection->escape($receiver), 
						$this->connection->escape($sender)
					)
			);
			
			if($this->result) 
			{

				$resultss = $this->results($this->result);
				
				// filter messages based on its storage
				foreach($resultss as $res)
				{
					// get only messages that are not deleted
					
					// just to order data based on logged_in user
					if($res['user_id'] == $sender || $res['receiver'] == $sender)
					{
						if(
							$res['user_id'] == $sender && $res['storage_a'] == 0 ||
							$res['user_id'] == $sender && $res['storage_a'] == 0 && $res['storage_b'] == 0 ||
							$res['user_id'] == $receiver && $res['storage_b'] == $res['user_id'] ||
							$res['user_id'] == $receiver && $res['storage_b'] == 0 && $res['storage_b'] == 0
						)
						{
							// kill it
						} else {
							$results[] = array(
								'id' => $res['id'],
								'user_id' => $res['user_id'],
								'receiver' => $res['receiver'],
								'storage_a' => $res['storage_a'],
								'storage_b' => $res['storage_b']
							);	
						}
					} 
				}
				
				if(!empty($results))
				{
					return $results;
				} else {
					return false;	
				}
				
			} else {
				return false;	
			}	
		}
		
		////////////////////////////////////////////
		// Get Server/Client Messages
		//////////////////////////////////////////
		public function get_messages($message_ID)
		{
			// Run querys based on weather the client has the message or not
			$this->result = $this->connection->query(
				sprintf(
					"SELECT messages, attachment FROM $this->table
					WHERE id = %s
					ORDER BY id", 
					$this->connection->escape($message_ID))
			);	
			
			$results = $this->results($this->result);
			
			if($this->result) 
			{
				// Emoticons
				$message = str_replace(array_keys($this->emoticons), array_values($this->emoticons), $results['0']['messages']);
				
				// Attachments
				if($results['0']['attachment'] !== 'false')
				{
					$message .= '<br /><img src="'.$this->attachmentPath.$results['0']['attachment'].'" />';	
				}
				
				return $message;
				
			} else {
				return false;	
			}
		}
		
		///////////////////////////////////////
		// Get Message (deprecated)
		/////////////////////////////////////
		public function get_last_message($id)
		{
			if(isset($_SESSION['jChat_requested_id']))
			{
				$id = $_SESSION['jChat_requested_id'];		
			} else {
				$id = 0;	
			}
			
			// Run querys based on weather the client has the message or not
			$this->result = $this->connection->query(
				sprintf(
					"SELECT id, messages FROM $this->table
					WHERE user_id = %s AND receiver = %s 
					|| user_id = %s AND receiver = %s
					ORDER BY id DESC LIMIT 1", 
					$this->connection->escape($this->clientID),
					$this->connection->escape($this->serverID),
					$this->connection->escape($this->serverID),
					$this->connection->escape($this->clientID)
					)
			);	
			
			$result = $this->connection->fetch_row($this->result);
			
			if($this->result) 
			{
				if($result['id'] == $id)
				{
					exit();
				}
				return $result;
			} else {
				return false;	
			}
		}
		
		public function get_last_message_with($id)
		{
			$this->result = $this->connection->query(
				sprintf(
					"SELECT id, user_id, receiver, storage_a, storage_b, messages FROM $this->table
					WHERE user_id = %s AND receiver = %s AND storage_b != 0
					ORDER BY id DESC LIMIT 1", 
					$this->connection->escape($id),
					$this->connection->escape($this->clientID)
					)
			);
			
			$result = $this->connection->fetch_row($this->result);
			
			if($result) 
			{
				if($result['id'] == $id)
				{
					exit();
				}
				return $result;
			} else {
				return false;	
			}
		}
		
		////////////////////////////////////////
		// Get Messages Time
		///////////////////////////////////////
		public function get_messages_time($ID)
		{
			// Result Query
			$this->result = $this->connection->query(sprintf("SELECT time FROM $this->table WHERE id = %s", $this->connection->escape($ID)));
			
			$results =  $this->connection->fetch_row($this->result);
			
			if($this->result) 
			{	
				return $this->time_calculation($results['time']);
			} else {
				return false;	
			}
		}
		
		public function get_session_time($serverID)
		{
			// Result Query
			$this->result =  $this->connection->query(sprintf("SELECT session_time FROM $this->users_table WHERE id = %s AND session = 'offline'",  $this->connection->escape($serverID)));
			
			$results =  $this->connection->fetch_row($this->result);
			
			if( $this->connection->num_rows($this->result) !== 0) 
			{	
				return $this->time_calculation($results['session_time']);
			} else {
				return false;	
			}	
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Get users from users table excluding the logged user because it does not makes sense chatting or messaging to yourself
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		public function get_users($clientID)
		{
			// Result Query
			$this->result =  $this->connection->query(sprintf("SELECT $this->users_usernameField, $this->user_idField, status, session FROM $this->users_table WHERE $this->user_idField != %s",  $this->connection->escape($clientID)));
			
			if($this->result) 
			{
				return $this->results($this->result);
			} else {
				return false;	
			}	
		}
		
		///////////////////////////////////////////////
		// Get id, username based on a user id
		/////////////////////////////////////////////
		public function get_user($serverID, $return)
		{
			// Result Query
			$this->result =  $this->connection->query(sprintf("SELECT $this->users_usernameField, $this->user_idField FROM $this->users_table WHERE $this->user_idField = %s",  $this->connection->escape($serverID)));
			
			if($this->result) 
			{
				$res =  $this->connection->fetch_row($this->result);

				switch($return)
				{
					case "ID":
						return $res[$this->user_idField];
						break;
					case "USERNAME":
						return $res[$this->users_usernameField];
						break;
				}
			} else {
				return false;	
			}	
		}
		
		//////////////////////////////////////////////////////////
		// find new received messages
		/////////////////////////////////////////////////////////
		public function get_unread_messages($serverID, $clientID)
		{
			$this->result =  $this->connection->query(sprintf("SELECT id FROM $this->table WHERE status = '%s' AND user_id = %s AND receiver = %s", 'unread',  $this->connection->escape($serverID),  $this->connection->escape($clientID)));
			
			$result = $this->connection->num_rows($this->result);
			
			if($result == 0)
			{
				return '';
			} else {
				return $result;	
			}
		}
		
		//////////////////////////////////////////////////////////
		// Get current user session
		/////////////////////////////////////////////////////////
		public function get_current_session($clientID)
		{
			$this->result =  $this->connection->query(sprintf("SELECT session FROM $this->users_table WHERE $this->user_idField = %s",  $this->connection->escape($clientID)));
			
			$result =  $this->connection->num_rows($this->result);
			
			if($result == 0)
			{
				return false;
			} else {
				$res =  $this->connection->fetch_row($this->result);
				return $res['session'];	
			}
		}
		
		/******************************************************************
		* Write Context
		*******************************************************************/
		
		///////////////////////////////////////
		// Set Messages
		/////////////////////////////////////
		public function set_message($message)
		{
			
			$attachment = 'false';
			
			$this->result =  $this->connection->query(
				sprintf("INSERT INTO $this->table
							SET 
							  messages = '%s',
							  time = NOW(),
							  user_id = %s,
							  receiver = %s,
							  storage_a = %s,
							  storage_b = %s,
							  status = 'unread',
							  attachment = '%s'
						", 
						  $this->connection->escape(htmlentities($message, ENT_COMPAT, 'UTF-8')),
						  $this->connection->escape($this->clientID),
						  $this->connection->escape($this->serverID),
						  $this->connection->escape($this->clientID),
						  $this->connection->escape($this->serverID),
						  $this->connection->escape($attachment)
				)
			);
			
			if($this->result)
			{
				return  $this->connection->insert_id();	
			} else {
				return false;	
			}	
		}
		
		//////////////////////////////////////////////
		// Set Messages to read
		////////////////////////////////////////////
		public function set_messages_read($serverID)
		{
			$this->result =  $this->connection->query(
				sprintf("UPDATE $this->table 
							SET 
							  status = '%s'
							WHERE
							  user_id = '%s' AND status = 'unread'
						", 
						'read',
						  $this->connection->escape($serverID) 
				)
			);
			
			if($this->result)
			{
				return true;	
			} else {
				return false;	
			}
		}
		
		////////////////////////////////////////////////////////////
		// Set User Session Status
		///////////////////////////////////////////////////////////
		public function set_user_sessionStatus($clientID, $status)
		{
			switch($status)
			{
				case "ONLINE":
					
					$this->result =  $this->connection->query(sprintf("SELECT session FROM $this->users_table WHERE $this->user_idField = '%s'",  $this->connection->escape($clientID)));
					
					$is_online =  $this->connection->fetch_row($this->result); 
										
					if($is_online['session'] == 'offline') // not online set online
					{
						$this->result =  $this->connection->query(
							sprintf("UPDATE $this->users_table
										SET 
										  session = '%s',
										  session_time = NOW()
										WHERE
										  $this->user_idField = %s
									", 
									'online',
									  $this->connection->escape($clientID) 
							)
						);
						
						if($this->result)
						{
							return true;	
						} else {
							return false;	
						}
					} else {
						return false;	
					}
					
				break;
				
				case "OFFLINE":
				
					$this->result =  $this->connection->query(
						sprintf("UPDATE $this->users_table
									SET 
									  session = '%s',
									  session_time = NOW()
									WHERE
									  $this->user_idField = %s
								", 
								'offline',
								  $this->connection->escape($clientID) 
						)
					);
					
					if($this->result)
					{
						return true;	
					} else {
						return false;	
					}
					
				break;
				
				case "BACK_ONLINE":
				
					$this->result =  $this->connection->query(
						sprintf("UPDATE $this->users_table
									SET 
									  session = '%s',
									  session_time = NOW()
									WHERE
									  $this->user_idField = %s
								", 
								'online',
								  $this->connection->escape($clientID) 
						)
					);
					
					if($this->result)
					{
						return true;	
					} else {
						return false;	
					}
					
				break;
			}
		}
		
		///////////////////////////////////////////////////////////////////////
		// Unregister Message
		/////////////////////////////////////////////////////////////////////
		public function unregister_message($message_id, $clientID, $serverID)
		{
			// Find Owner
			$this->result =  $this->connection->query(
				sprintf("SELECT user_id, receiver, storage_a, storage_b FROM $this->table
							WHERE
							  id = %s
						", 
						  $this->connection->escape($message_id)
				)
			);	
			
			$row =  $this->connection->fetch_row($this->result);
			
			// Delete Magic
			if($row['user_id'] == $clientID || $row['receiver'] == $clientID)
			{
				if($row['user_id'] == $clientID)
				{
					$internal_storage_a = 0;
					$internal_storage_b = $row['storage_b'];
				}
				
				if($row['receiver'] == $clientID)
				{
					$internal_storage_a = $row['storage_a'];
					$internal_storage_b = 0;
				} 
			} 		
			
			// Register Storages (a => client, b => server)
			$updated =  $this->connection->query(
				sprintf("UPDATE $this->table
							SET 
							  storage_a = %s,
							  storage_b = %s
							WHERE
							  id = %s
						", 
						  $this->connection->escape($internal_storage_a),
						  $this->connection->escape($internal_storage_b),
						  $this->connection->escape($message_id)
				)
			);	
			
			if($updated)
			{
				// check if both client and server does not have the messages to remove them
				// If you want to never delete messages remove this code and left just return true;
				
				// Look again
				$this->result =  $this->connection->query(
					sprintf("SELECT storage_a, storage_b FROM $this->table
								WHERE
								  id = %s
							", 
							  $this->connection->escape($message_id)
					)
				);	
				$row =  $this->connection->fetch_row($this->result);
				
				// permanent delete it
				if($row['storage_a'] == 0 && $row['storage_b'] == 0)
				{
					 $this->connection->query(
						sprintf("DELETE FROM $this->table
									WHERE
									  id = %s
								", 
								  $this->connection->escape($message_id)
						)
					);			
				}
				
				return true;
				
			} else {
				return false;	
			}

		}
		
	}
	
?>