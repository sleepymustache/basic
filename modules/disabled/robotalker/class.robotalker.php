<?php
/**
 * @page robo1 RoboTalker
 *
 * RoboTalker class to create, remove, and view scheduled calls.
 *
 * @section usage Usage:
 *
 * @code
 * 	$w = new RoboTalker;
 *
 * 	$result = $w->add(5625654544);
 *
 * 	$result = $w->delete(5625654544);
 *
 * 	$result = $w->GetScheduledCalls();
 * @endcode
 *
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.0
 * @copyright  GPL 3 http://cuttingedgecode.com
 */

/**
 * The base Request object
 */
class Request {
	/**
	 * String Session ID of the request
	 */
	public $SessionId;
}

/**
 * The object sent for the LoginRequest method
 */
class LoginRequest extends Request {
	/**
	 * String User ID of the Login Request
	 */
	public $UserId;

	/**
	 * String Password for the User
	 */
	public $Password;
}

/**
 * The object sent for the GetCallsRequest method
 */
class GetCallsRequest extends Request {
	/**
	 * DateTime The Start date
	 */
	public $StartDate;

	/**
	 * DateTime The End Date
	 */
	public $EndDate;
}

/**
 *
 */
class GetCampaignStatsRequest extends Request {
	/**
	 * Int, The Campaign ID
	 */
	public $CampaignId;
	public $IncludeDetails;

	public function __construct() {
		$this->IncludeDetails = true;
	}
}

/**
 * The object sent for the DeleteCallsRequest method
 */
class DeleteCallsRequest extends Request {
	/**
	 * array IDs to delete
	 */
	public $Ids;
}

/**
 * The object sent for the ScheduleCalls method
 */
class ScheduleCallsRequestOfInt32 extends Request {
	/**
	 * MessageRecipient<int32> Mandatory.
	 */
	public $Recipients;

	/**
	 * String Optional.
	 */
	//public $DialerNumber;

	/**
	 * String Optional.
	 */
	public $Message;

	/**
	 * Int Voicemail Message ID
	 */
	public $VoiceMailMessage;

	/**
	 * CampaignTypes Optional.
	 */
	public $ScheduleType;

	/**
	 * DateTime The Start Time
	 */
	public $StartTime;

	/**
	 * DateTime The End time
	 */
	public $EndTime;

	/**
	 * WeekDays The Days of the week to run schedule
	 */
	public $Days;

	/**
	 * Int How ofter to run the schedule
	 */
	public $Interval;

	/**
	 * Int Schedule priority
	 */
	public $Priority;

	/**
	 * Bool Do you want a confirmation email?
	 */
	public $Confirmation;
}

/**
 * Used for adding a call in RoboTalker::add()
 */
class ArrayOfMessageRecipientOfInt32 {
	public $MessageRecipientOfInt32;
}

/**
 * Recipeient used in ScheduleCalls
 */
class MessageRecipientOfInt32 {
	/**
	 * String Optional. Recipient Name
	 */
	//public $Name;

	/**
	 * String Phone number to send message to
	 */
	public $DialNumber;

	/**
	 * Int Optional. Message Identifier.
	 */
	//public $Message;
}

/**
 * The RoboTalker class controls the communication to the WebService
 */
class RoboTalker {
	/**
	 * SoapClient() a SOAP Client Object
	 * @private
	 */
	private $WebService;

	/**
	 * String The WSDL URI to use
	 * @private
	 */
	private $wsdl = "http://service.robotalker.com/robotalker.asmx?WSDL";

	/**
	 * String The Username of the RoboTalker Account
	 * @private
	 */
	private $UserId = 'lmize@kaneandfinkel.com';

	/**
	 * String The password for the RoboTalker Account
	 * @private
	 */
	private $Password = 'bayer2011';

	/**
	 * Array The SOAP Options
	 * @private
	 */
	private $soap_options = array(
		'trace' => 1,
		'exceptions' => 1
	);

	/**
	 * Int The message ID of the message to send
	 * @private
	 */
	private $messageId = 39147;

	/**
	 * Bool Leave a voicemail?
	 * @private
	 */
	 private $voicemail = false;

	/**
	 * Connects to webservice, login, store SessionId.
	 *
	 * @return instance
	 */
	public function __construct() {
		$this->WebService = new SoapClient($this->wsdl, $this->soap_options);
		$this->Login();
	}

	/**
	 * Log in to RoboTalker Service
	 *
	 * @private
	 * @return string SessionId
	 */
	private function Login() {
		$r = new StdClass();
		$r->request = new LoginRequest();
		$r->request->UserId = $this->UserId;
		$r->request->Password = $this->Password;

		$result = $this->WebService->Login($r);

		if ($result->LoginResult->Error <> "None") {
			throw new Exception($result->LoginResult->Error);
		} else {
			$this->SessionId = $result->LoginResult->SessionId;
			return true;
		}
	}

	/**
	 * Gets all scheduled calls
	 *
	 * @private
	 * @return array Calls objects
	 */
	public function GetScheduledCalls() {
		$r = new StdClass();
		$r->request = new getCallsRequest();
		$r->request->SessionId = $this->SessionId;
		$r->request->StartDate = date(DATE_ATOM, mktime(date("G"), date("i"), 0, date("m"), date("d"), date("Y")));
		$r->request->EndDate = date(DATE_ATOM, mktime(date("G"), date("i"), 0, date("m"), date("d"), 2111));

		$result = $this->WebService->GetScheduledCalls($r);

		if ($result->GetScheduledCallsResult->Error <> "None") {
			throw new Exception($result->GetScheduledCallsResult->Error);
		} else {
			return $result->GetScheduledCallsResult->Calls;
		}
	}

	/**
	 * Gets all Campaign Stats
	 *
	 * @private
	 * @param $id
	 * @return array Calls objects
	 */
	public function GetCampaignStats($id) {
		$r = new StdClass();
		$r->request = new GetCampaignStatsRequest();
		$r->request->SessionId = $this->SessionId;
		$r->request->IncludeDetails = true;
		$r->request->CampaignId = $id;

		$result = $this->WebService->GetCampaignStats($r);

		if ($result->GetCampaignStatsResult->Error <> "None") {
			throw new Exception($result->GetCampaignStatsResult->Error);
		} else {
			return $result->GetCampaignStatsResult;
		}
	}

	/**
	 * Deletes scheduled calls
	 *
	 * @param array $Ids An array of integers
	 * @private
	 * @return array Ids of all removed Ids
	 */
	private function DeleteScheduledCalls($Ids) {
		$r = new StdClass();
		$r->request = new DeleteCallsRequest();
		$r->request->SessionId = $this->SessionId;
		$r->request->Ids = $Ids;

		$result = $this->WebService->DeleteScheduledCalls($r);

		if ($result->DeleteScheduledCallsResult->Error <> "None") {
			throw new Exception($result->DeleteScheduledCallsResult->Error);
		} else {
			return $result->DeleteScheduledCallsResult->RemovedIds;
		}
	}

	/**
	 * Schedules a new call
	 *
	 * @todo Calculate StartTime and EndTime, ask Leah if they have a
	 * preference for when the call should be made
	 * @param array $recipients Array of complexType MessageRecipient<Int32>
	 * @return int The ID of the newly created Campaign
	 */
	private function ScheduleCalls($recipients) {
		$hours = date("G") + 5;
		$minutes = date("i");
		$seconds = date("s");
		$months = date("m");
		$day = date("d");
		$year = date("Y");

		$r = new StdClass();
		$r->request = new ScheduleCallsRequestOfInt32();
		$r->request->SessionId = $this->SessionId;
		$r->request->Message = $this->messageId;
		$r->request->Recipients = $recipients;

		if ($this->voicemail == true) {
			$r->request->VoiceMailMessage = $this->messageId;
		} else {
			$r->request->VoiceMailMessage = 0;
		}

		for ($i = 0; $i < 1; $i++) {
			$minutes = $minutes + 5;

			$StartTime = date(DATE_ATOM, mktime($hours, $minutes, $seconds, $months, $day, $year));

			$r->request->StartTime = $StartTime;
			$result = $this->WebService->ScheduleCalls($r);

			if ($result->ScheduleCallsResult->Error <> "None") {
				throw new Exception($result->ScheduleCallsResponse->Error);
			}
		}

		return $result->ScheduleCallsResult->CampaignId;

	}

	/**
	 * Add a new Call.
	 * @param  int $phoneNumber [description]
	 * @throws Exception
	 * @return int CampaignId
	 */
	public function add($phoneNumber) {
		$r = new MessageRecipientOfInt32;
		$r->DialNumber = $phoneNumber;
		$r->Message = $this->messageId;

		$recipients = new ArrayOfMessageRecipientOfInt32;
		$recipients->MessageRecipientOfInt32[] = $r;

		try {
			$result = $this->ScheduleCalls($recipients);
			return $result;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Deletes a Call.
	 * @param  String $phoneNumber A phone number to delete
	 * @throws Exception If Delete fails
	 * @return array An Array of phone numbers deleted.
	 */
	public function delete ($phoneNumber) {

		$calls = $this->GetScheduledCalls();

		if (is_array($calls->ScheduledCall)) {
			foreach ($calls->ScheduledCall as $call) {
				$numbersToDelete = array();

				if ($call->DialNumber == $phoneNumber) {
					$numbersToDelete[] = $call->Id;

					try {
						$deleted = $this->DeleteScheduledCalls($numbersToDelete);
					} catch (Exception $e) {
						throw new Exception($e->getMessage());
					}
				}
			}
		} else {
			if (isset($calls->ScheduledCall)) {
				$numbersToDelete = array();

				if ($calls->ScheduledCall->DialNumber == $phoneNumber) {
					$numbersToDelete[] = $calls->ScheduledCall->Id;

					try {
						$deleted = $this->DeleteScheduledCalls($numbersToDelete);
					} catch (Exception $e) {
						throw new Exception($e->getMessage());
					}
				}
			}
		}

		return true;
	}

	/**
	 * Toggle a voicemail.
	 * @return nothing
	 */
	public function sendVoicemail() {
		$this->voicemail = true;
	}
}