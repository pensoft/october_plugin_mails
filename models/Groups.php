<?php namespace Pensoft\Mails\Models;

use Backend\Facades\BackendAuth;
use Cms\Classes\Theme;
use Illuminate\Support\Facades\DB;
use Model;

/**
 * Model
 */
class Groups extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pensoft_mails_groups';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

	/**
	 * Validate goto and reply_to emails
	 */
	public function beforeSave()
	{
        $theme = Theme::getActiveTheme();
		$arrGoto = array();
		if ($this->goto != '') {
			$arrGoto = explode(',', $this->goto);
			foreach($arrGoto AS $gotoEmailAddress) {
				$isValid = filter_var($gotoEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
				if(!$isValid){
					throw new \ValidationException([
						'goto' => $gotoEmailAddress. ' is not valid GOTO email'
					]);
				}
			}
			$arrGoto = array_map('strtolower', $arrGoto);
			$arrGoto = array_unique($arrGoto);
			$this->goto = implode(',', $arrGoto);

		}else{
			throw new \ValidationException([
				'goto' => 'Goto is required!'
			]);
		}

		$arrReply = array();
		if ($this->reply_to != '') {
			$arrReply = explode(',', $this->reply_to);
			foreach($arrReply AS $replyEmailAddress) {
				$isValid = filter_var($replyEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
				if(!$isValid){
					throw new \ValidationException([
						'reply_to' => $replyEmailAddress. ' is not valid REPLY TO email'
					]);
				}
			}
			$arrReply = array_map('strtolower', $arrReply);
			$arrReply = array_unique($arrReply);
			$this->reply_to = implode(',', $arrReply);
		}

		//TODO
        $arrReplaceFrom = array();
        if ($this->replace_from != '') {
            $arrReplaceFrom = explode(',', $this->replace_from);
            foreach($arrReplaceFrom AS $replaceFromEmailAddress) {
                $isValidReplaceFrom = filter_var($replaceFromEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
                if(!$isValidReplaceFrom){
                    throw new \ValidationException([
                        'replace_from' => $replaceFromEmailAddress. ' is not valid REPLACE FROM email'
                    ]);
                }
            }
            $arrReplaceFrom = array_map('strtolower', $arrReplaceFrom);
            $arrReplaceFrom = array_unique($arrReplaceFrom);
            $this->replace_from = implode(',', $arrReplaceFrom);
        }

        $arrReplaceTo = array();
        if ($this->replace_to != '') {
            $arrReplaceTo = explode(',', $this->replace_to);
            foreach($arrReplaceTo AS $replaceToEmailAddress) {
                $isValidReplaceTo = filter_var($replaceToEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
                if(!$isValidReplaceTo){
                    throw new \ValidationException([
                        'replace_to' => $replaceToEmailAddress. ' is not valid REPLACE TO email'
                    ]);
                }
            }
            $arrReplaceTo = array_map('strtolower', $arrReplaceTo);
            $arrReplaceTo = array_unique($arrReplaceTo);
            $this->replace_to = implode(',', $arrReplaceTo);
        }


		/**
		 * Update moderators field TODO slect goto and replyto of all groups
		 */
		$allModerators = array_unique(array_merge(array('root@psweb.pensoft.net', 'messaging@pensoft.net'), $arrGoto, $arrReply));
		$allModerators = array_map('strtolower', $allModerators);
		$this->moderators = implode(',', $allModerators);
        $this->domain = $theme->site_domain ?? $_SERVER['SERVER_NAME'];

        // make sure we've got a valid email
        $isValidGroupEmail = filter_var($this->address, FILTER_VALIDATE_EMAIL); // boolean
        if(!$isValidGroupEmail){
            throw new \ValidationException([
                'address' => $this->address. ' is not valid GROUP email'
            ]);
        }

        $groupEmailDomain = substr(strrchr($this->address, "@"), 1);
        if($groupEmailDomain != $this->domain){
            throw new \ValidationException([
                'address' => $this->address. ' domain doesn\'t match the site domain '.$this->domain
            ]);
        }
	}

	public function afterSave()
	{
		$groupEmail = $this->address;
		$groupMembers = $this->goto;
		$groupDomain = $this->domain;
		$groupModerators = $this->moderators;
		$replyTo = $this->reply_to;
		$active = $this->active;
		$accesspolicy = $this->accesspolicy;

		DB::connection('vmail')->select('SELECT * FROM savemailgroup(\'' . $groupEmail . '\', \'' . trim($groupMembers) . '\', \'' . $groupDomain . '\',  \'' . trim($groupModerators) . '\',  \'' . trim($replyTo) . '\', ' . (int)$active . ',  \'' . trim($accesspolicy) . '\')');

        $replaceFrom = $this->replace_from;
        $replaceTo = $this->replace_to;
        $nameAppend = $this->name_append;
        $addReplyTo = $this->add_reply_to;

        DB::connection('vmail')->select('SELECT * FROM savereplaceoptions(\'' . $groupEmail . '\', \'' . trim($replaceFrom) . '\', \'' . trim($replaceTo) . '\', \'' . trim($nameAppend) . '\', \'' . trim($addReplyTo) . '\', ' . (int)$active . ')');

		return \Redirect::refresh();
	}

    public function filterFields($fields, $context = null){
        if ($context == 'update') {
            $fields->address->disabled = true;
        }
        $user = BackendAuth::getUser();
        if(!$user->is_superuser){
            $fields->replace_from->disabled = true;
            $fields->replace_to->disabled = true;
            $fields->name_append->disabled = true;
            $fields->add_reply_to->disabled = true;
        }
	}
}
